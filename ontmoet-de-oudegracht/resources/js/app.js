import L from 'leaflet';

const mapElement = document.querySelector('#post-map');

if (mapElement) {
    const video = document.querySelector('#main-video');
    const textArea = document.querySelector('#post-text-area');
    const posts = JSON.parse(mapElement.dataset.posts || '[]');

    const map = L.map(mapElement);

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
        title.className = 'text-2xl font-bold';
        title.textContent = post.title;
        article.append(title);

        if (post.place) {
            const place = document.createElement('p');
            place.textContent = post.place;
            article.append(place);
        }

        if (post.date) {
            const date = document.createElement('time');
            date.dateTime = post.date;
            date.textContent = formatDate(post.date);
            article.append(date);
        }

        const content = document.createElement('p');
        content.textContent = post.text_content;
        article.append(content);

        ['image', 'image2', 'image3', 'image4', 'image5'].forEach((field) => {
            if (!post[field]) return;

            const image = document.createElement('img');
            image.src = `/storage/${post[field]}`;
            image.alt = '';
            image.className = 'mt-2 max-h-48 w-full object-cover';
            article.append(image);
        });

        textArea.append(article);
    }

    // Marker locations are collected so the map can show all post markers.
    const markerLocations = [];

    // Each coordinate is one point in the Oudegracht route.
    // The boat will travel through these points from Gaardbrug to Stadhuisbrug.
    const boatRoute = [
        [52.0891988, 5.1211682],
        [52.0894500, 5.1209800],
        [52.0897800, 5.1208200],
        [52.0901300, 5.1206700],
        [52.0904700, 5.1204900],
        [52.0908200, 5.1203000],
        [52.0911700, 5.1200800],
        [52.0915523, 5.1197017],
    ];

    posts.forEach((post) => {
        if (post.latitude === null || post.longitude === null) return;

        const location = [Number(post.latitude), Number(post.longitude)];
        markerLocations.push(location);

        const marker = L.marker(location).addTo(map);
        marker.on('click', () => {
            renderPost(post);

            if (video && post.video_timestamp !== null) {
                video.currentTime = Number(post.video_timestamp);
                video.play().catch(() => {});
            }
        });
    });

    // The polyline only draws the visual canal route on the map.
    L.polyline(boatRoute, {
        color: '#ffffff',
        weight: 4,
        opacity: 0.8,
        dashArray: '2, 8',
    }).addTo(map);

    map.fitBounds([...markerLocations, ...boatRoute], { padding: [30, 30] });

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
