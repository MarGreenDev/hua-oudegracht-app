import { getVideoRouteProgress } from './video-route.js';

const postModal = document.querySelector('#post-modal');
const postForm = document.querySelector('#post-form');

if (postModal && postForm) {
    const postFields = ['title', 'place', 'text_content', 'date', 'latitude', 'longitude'];
    const imageFields = ['image', 'image2', 'image3', 'image4', 'image5'];
    const methodField = postForm.elements.namedItem('_method');
    const postIdField = postForm.elements.namedItem('post_id');
    const timestampDisplay = document.querySelector('#video-timestamp-display');
    const timestampStatus = document.querySelector('#video-timestamp-status');
    const route = JSON.parse(postForm.dataset.videoRoute || '[]');
    const videoDuration = Number(postForm.dataset.videoDuration);

    function updateVideoTimestamp() {
        const latitudeField = postForm.elements.namedItem('latitude');
        const longitudeField = postForm.elements.namedItem('longitude');
        const latitude = Number(latitudeField.value);
        const longitude = Number(longitudeField.value);

        if (
            !latitudeField.value.trim()
            || !longitudeField.value.trim()
            || !Number.isFinite(latitude)
            || !Number.isFinite(longitude)
            || latitude < -90
            || latitude > 90
            || longitude < -180
            || longitude > 180
            || route.length < 2
        ) {
            timestampDisplay.value = '';
            timestampStatus.textContent = 'Enter a valid latitude and longitude to calculate the timestamp.';
            return;
        }

        if (!Number.isFinite(videoDuration) || videoDuration <= 0) {
            timestampDisplay.value = '';
            timestampStatus.textContent = 'The video duration is not configured.';
            return;
        }

        const progress = getVideoRouteProgress(latitude, longitude, route);
        timestampDisplay.value = (progress * videoDuration).toFixed(3);
        timestampStatus.textContent = 'Calculated from the location along the video route.';
    }

    ['latitude', 'longitude'].forEach((field) => {
        postForm.elements.namedItem(field).addEventListener('input', updateVideoTimestamp);
    });

    postForm.addEventListener('submit', (event) => {
        updateVideoTimestamp();
        if (!timestampDisplay.value) {
            event.preventDefault();
        }
    });

    function clearImagePreviews() {
        imageFields.forEach((field) => {
            document.querySelector(`#${field}-preview`).replaceChildren();
        });
    }

    function showImagePreviews(post) {
        imageFields.forEach((field) => {
            if (!post[field]) return;

            const image = document.createElement('img');
            image.src = `/storage/${post[field]}`;
            image.alt = `Current ${field} image`;
            image.className = 'mt-1 max-h-24 w-auto';
            document.querySelector(`#${field}-preview`).append(image);
        });
    }

    function openCreatePost() {
        postForm.reset();
        postForm.action = postForm.dataset.storeUrl;
        postIdField.value = '';
        methodField.value = '';
        document.querySelector('#post-modal-title').textContent = 'New post';
        document.querySelector('#post-submit-button').textContent = 'Create post';
        clearImagePreviews();
        postModal.showModal();
    }

    function openEditPost(post, updateUrl) {
        postForm.reset();
        postForm.action = updateUrl.replace('__POST__', encodeURIComponent(post.id));
        postIdField.value = post.id;
        methodField.value = 'PATCH';
        postFields.forEach((field) => {
            postForm.elements.namedItem(field).value = post[field] ?? '';
        });
        updateVideoTimestamp();
        clearImagePreviews();
        showImagePreviews(post);
        document.querySelector('#post-modal-title').textContent = 'Edit post';
        document.querySelector('#post-submit-button').textContent = 'Save changes';
        postModal.showModal();
    }

    document.querySelector('#create-post-button').addEventListener('click', openCreatePost);
    document.querySelector('#close-post-modal').addEventListener('click', () => postModal.close());
    document.querySelector('#cancel-post-modal').addEventListener('click', () => postModal.close());

    document.querySelectorAll('.edit-post-button').forEach((button) => {
        button.addEventListener('click', () => {
            openEditPost(JSON.parse(button.dataset.post), button.dataset.updateUrl);
        });
    });

    if (postForm.dataset.validationPostId) {
        postForm.action = postForm.dataset.updateUrl.replace(
            '__POST__',
            encodeURIComponent(postForm.dataset.validationPostId),
        );
        document.querySelector('#post-modal-title').textContent = 'Edit post';
        document.querySelector('#post-submit-button').textContent = 'Save changes';

        const post = [...document.querySelectorAll('.edit-post-button')]
            .map((button) => JSON.parse(button.dataset.post))
            .find((item) => String(item.id) === postForm.dataset.validationPostId);
        if (post) showImagePreviews(post);
    }

    if (postForm.dataset.openOnLoad === 'true') {
        postModal.showModal();
        updateVideoTimestamp();
    }
}
