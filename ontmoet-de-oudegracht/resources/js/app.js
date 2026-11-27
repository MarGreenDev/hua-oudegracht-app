import L from 'leaflet';
import './cms.js';
import { getVideoRouteProgress } from './video-route.js';

const introDialog = document.querySelector('#intro-dialog');

if (introDialog instanceof HTMLDialogElement) {
    introDialog.showModal();
}

const mapElement = document.querySelector('#post-map');

if (mapElement) {
    const video = document.querySelector('#main-video');
    const textArea = document.querySelector('#post-text-area');
    const posts = JSON.parse(mapElement.dataset.posts || '[]');
    const boatRoute = JSON.parse(mapElement.dataset.videoRoute || '[]');

    const map = L.map(mapElement, {
        dragging: false,
        touchZoom: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        zoomControl: false,
    });
    L.Icon.Default.mergeOptions({
        iconSize: [44, 54],
        iconAnchor: [22, 54],
        popupAnchor: [1, -48],
        shadowSize: [60, 60],
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    function formatDate(date) {
        if (!date) return '';
        const [year, month, day] = date.split('-');
        return `${day}-${month}-${year}`;
    }

    function renderPost(post) {
        textArea.replaceChildren();

        const article = document.createElement('article');
        const title = document.createElement('h2');
        title.className = 'experience-post-title';
        title.textContent = post.title;
        article.append(title);

        if (post.place) {
            const place = document.createElement('p');
            place.className = 'experience-post-meta';
            place.textContent = post.place;
            article.append(place);
        }

        if (post.date) {
            const date = document.createElement('time');
            date.className = 'experience-post-meta';
            date.dateTime = post.date;
            date.textContent = formatDate(post.date);
            article.append(date);
        }

        const content = document.createElement('p');
        content.className = 'experience-post-content';
        content.textContent = post.text_content;
        article.append(content);

        ['image', 'image2', 'image3', 'image4', 'image5'].forEach((field) => {
            if (!post[field]) return;

            const image = document.createElement('img');
            image.src = `/storage/${post[field]}`;
            image.alt = '';
            image.className = 'experience-post-image';
            article.append(image);
        });

        textArea.append(article);
    }

    let activeVideoSeek = 0;
    let activeSeekFrame = null;
    let activeReverseSeek = null;
    // Use normal playback for fast-forward; the browser doesn't support reverse playback reliably.
    const seekPlaybackRate = 16;

    function seekVideoToTimestamp(timestamp) {
        if (!video || !Number.isFinite(timestamp) || timestamp < 0) return;

        const seekId = ++activeVideoSeek;
        // Stop any previous marker seek so only the most recently clicked marker controls playback.
        if (activeSeekFrame !== null) cancelAnimationFrame(activeSeekFrame);
        if (activeReverseSeek !== null) clearInterval(activeReverseSeek);
        video.pause();
        video.playbackRate = 1;

        const seek = () => {
            if (seekId !== activeVideoSeek) return;

            const targetTime = Math.min(timestamp, video.duration);
            if (!Number.isFinite(targetTime)) return;

            const resumeAtTarget = () => {
                if (seekId !== activeVideoSeek) return;
                video.playbackRate = 1;
                video.play().catch(() => {});
            };

            const finishSeek = () => {
                if (seekId !== activeVideoSeek) return;
                if (activeSeekFrame !== null) cancelAnimationFrame(activeSeekFrame);
                if (activeReverseSeek !== null) clearInterval(activeReverseSeek);
                activeSeekFrame = null;
                activeReverseSeek = null;
                video.pause();
                video.playbackRate = 1;

                // Correct any small timing difference, then resume at normal speed.
                if (Math.abs(video.currentTime - targetTime) < 0.05) {
                    resumeAtTarget();
                    return;
                }

                video.addEventListener('seeked', resumeAtTarget, { once: true });
                video.currentTime = targetTime;
            };

            const difference = targetTime - video.currentTime;
            if (Math.abs(difference) < 0.05) {
                finishSeek();
            } else if (difference > 0) {
                // Let the browser play forward quickly while monitoring for the target.
                video.playbackRate = seekPlaybackRate;
                video.play().catch(() => {
                    if (seekId !== activeVideoSeek) return;
                    if (activeSeekFrame !== null) cancelAnimationFrame(activeSeekFrame);
                    activeSeekFrame = null;
                    video.playbackRate = 1;
                });

                const advance = () => {
                    if (seekId !== activeVideoSeek) return;
                    if (video.currentTime >= targetTime) {
                        finishSeek();
                        return;
                    }
                    activeSeekFrame = requestAnimationFrame(advance);
                };
                activeSeekFrame = requestAnimationFrame(advance);
            } else {
                // Simulate rewind by stepping currentTime backward, then seek precisely at the target.
                let previousTime = performance.now();
                activeReverseSeek = setInterval(() => {
                    if (seekId !== activeVideoSeek) return;

                    const now = performance.now();
                    const elapsed = (now - previousTime) / 1000;
                    previousTime = now;
                    const nextTime = video.currentTime - elapsed * seekPlaybackRate;

                    if (nextTime <= targetTime) {
                        finishSeek();
                    } else {
                        video.currentTime = nextTime;
                    }
                }, 25);
            }
        };

        if (video.readyState >= HTMLMediaElement.HAVE_METADATA) {
            seek();
        } else {
            video.addEventListener('loadedmetadata', seek, { once: true });
        }
    }

    function seekVideoToPost(post) {
        if (!video) return;

        const seek = () => {
            let timestamp = post.video_timestamp === null || post.video_timestamp === undefined
                ? NaN
                : Number(post.video_timestamp);

            if (
                timestamp === 0
                && Number.isFinite(video.duration)
                && video.duration > 0
                && boatRoute.length >= 2
            ) {
                const progress = getVideoRouteProgress(
                    Number(post.latitude),
                    Number(post.longitude),
                    boatRoute,
                );
                if (progress > 0) timestamp = progress * video.duration;
            }

            if (!Number.isFinite(timestamp) || timestamp < 0) return;

            seekVideoToTimestamp(timestamp);
        };

        if (video.readyState >= HTMLMediaElement.HAVE_METADATA) {
            seek();
        } else {
            video.addEventListener('loadedmetadata', seek, { once: true });
        }
    }

    // Marker locations are collected so the map can show all post markers.
    const markerLocations = [];

    // Each coordinate is one point in the Oudegracht route.
    // The boat will travel through these points from Gaardbrug to Stadhuisbrug.
    posts.forEach((post) => {
        if (post.latitude === null || post.longitude === null) return;

        const location = [Number(post.latitude), Number(post.longitude)];
        markerLocations.push(location);

        const marker = L.marker(location).addTo(map);
        marker.on('click', () => {
            renderPost(post);
            seekVideoToPost(post);
        });
    });

    // The polyline only draws the visual canal route on the map.
    L.polyline(boatRoute, {
        color: '#ffffff',
        weight: 4,
        opacity: 0.8,
        dashArray: '2, 8',
    }).addTo(map);

    const mapLocations = boatRoute.length > 0 ? boatRoute : markerLocations;
    if (mapLocations.length > 0) {
        map.fitBounds(mapLocations, { padding: [30, 30] });
    }

    // A Leaflet divIcon lets us use inline SVG instead of a standard image marker.
    const boatIcon = L.divIcon({
        className: 'boat-marker',
        html: `
            <svg viewBox="0 0 40 40" aria-hidden="true">
                <path d="M8 26h24l-4 7H12l-4-7Z" fill="#c9a800" stroke="#002558" stroke-width="2"/>
                <path d="M20 7v19" stroke="#002558" stroke-width="2"/>
                <path d="M20 9l11 13H20V9Z" fill="#ffffff" stroke="#002558" stroke-width="1.5"/>
                <path d="M20 9l-9 13h9V9Z" fill="#d9e2ef" stroke="#002558" stroke-width="1.5"/>
            </svg>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
    });

    // Start the boat at the first coordinate before the first video event arrives.
    const boat = L.marker(boatRoute[0], {
        icon: boatIcon,
        zIndexOffset: 1000,
        keyboard: false,
        interactive: false,
    }).addTo(map);

    // Split the route into separate line segments for movement and heading calculations.
    const routeSegments = [];
    let routeLength = 0;

    // Calculate every segment's length in metres and its starting distance.
    for (let i = 1; i < boatRoute.length; i++) {
        const start = L.latLng(boatRoute[i - 1]);
        const end = L.latLng(boatRoute[i]);
        const length = start.distanceTo(end);

        routeSegments.push({ start, end, length, startDistance: routeLength });
        routeLength += length;
    }

    // Calculate the boat's compass heading between two geographic points.
    function getBearing(start, end) {
        const startLat = start.lat * Math.PI / 180;
        const endLat = end.lat * Math.PI / 180;
        const longitudeDifference = (end.lng - start.lng) * Math.PI / 180;

        const y = Math.sin(longitudeDifference) * Math.cos(endLat);
        const x =
            Math.cos(startLat) * Math.sin(endLat)
            - Math.sin(startLat) * Math.cos(endLat) * Math.cos(longitudeDifference);

        return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
    }

    // Convert the video's playback position into a distance along the complete route.
    function syncBoat() {
        if (!video || routeLength === 0) return;

        // Clamp the progress between 0 and 1 so it never leaves the route.
        const duration = Number.isFinite(video.duration) ? video.duration : 0;
        const progress = duration > 0
            ? Math.max(0, Math.min(1, video.currentTime / duration))
            : 0;
        const targetDistance = progress * routeLength;

        // Find the route segment containing the target distance.
        const segment = routeSegments.find(
            (item) => targetDistance <= item.startDistance + item.length,
        ) || routeSegments[routeSegments.length - 1];

        const segmentProgress = segment.length > 0
            ? (targetDistance - segment.startDistance) / segment.length
            : 0;
        // Interpolate latitude and longitude inside the active segment.
        const lat =
            segment.start.lat
            + (segment.end.lat - segment.start.lat) * segmentProgress;
        const lng =
            segment.start.lng
            + (segment.end.lng - segment.start.lng) * segmentProgress;
        const position = L.latLng(lat, lng);

        boat.setLatLng(position);
        const boatElement = boat.getElement();

        if (boatElement) {
            const bearing = getBearing(segment.start, segment.end);
            // Rotate only the SVG sail so the Leaflet marker itself stays positioned correctly.
            const svg = boatElement.querySelector('svg');
            if (svg) svg.style.transform = `rotate(${bearing}deg)`;
        }
    }

    // Synchronize once immediately, then again whenever the video changes position.
    syncBoat();
    video?.addEventListener('loadedmetadata', syncBoat);
    video?.addEventListener('timeupdate', syncBoat);
    video?.addEventListener('seeked', syncBoat);
}
