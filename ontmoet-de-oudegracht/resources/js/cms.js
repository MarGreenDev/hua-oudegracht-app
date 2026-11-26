const postModal = document.querySelector('#post-modal');
const postForm = document.querySelector('#post-form');

if (postModal && postForm) {
    const postFields = ['title', 'place', 'text_content', 'date', 'latitude', 'longitude', 'video_timestamp'];
    const imageFields = ['image', 'image2', 'image3', 'image4', 'image5'];
    const methodField = postForm.elements.namedItem('_method');
    const postIdField = postForm.elements.namedItem('post_id');

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
    }
}
