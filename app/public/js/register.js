document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    await handleFormSubmission({
        form: e.target,
        url: '/api/register',
        getFormData: () => getFormDataObject(['login', 'email', 'password']),
        submitBtn: document.getElementById('submitBtn'),
        errorAlert: document.getElementById('errorAlert'),
        successAlert: document.getElementById('successAlert'),
        loadingText: 'Registering...',
        defaultBtnText: 'Register',
        onSuccess: () => {
            document.getElementById('registerForm').reset();
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        }
    });
});