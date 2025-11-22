document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    await handleFormSubmission({
        form: e.target,
        url: '/api/login',
        getFormData: () => getFormDataObject(['login', 'password']),
        submitBtn: document.getElementById('submitBtn'),
        errorAlert: document.getElementById('errorAlert'),
        successAlert: document.getElementById('successAlert'),
        loadingText: 'Logging in...',
        defaultBtnText: 'Login',
        onSuccess: () => {
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        }
    });
});