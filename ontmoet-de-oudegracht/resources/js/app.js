import L from 'leaflet';

const mapElement = document.querySelector('#post-map');

if (mapElement) {
    const video = document.querySelector('#main-video');
    const textArea = document.querySelector('#post-text-area');
    const posts = JSON.parse(mapElement.dataset.posts || '[]');

    const map = L.map(mapElement).setView([52.0897, 5.1213], 15);

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

    posts.forEach((post) => {
        if (post.latitude === null || post.longitude === null) return;

        const marker = L.marker([Number(post.latitude), Number(post.longitude)]).addTo(map);
        marker.on('click', () => {
            renderPost(post);

            if (video && post.video_timestamp !== null) {
                video.currentTime = Number(post.video_timestamp);
                video.play().catch(() => {});
            }
        });
    });
}
