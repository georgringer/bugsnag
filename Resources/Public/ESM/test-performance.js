document.addEventListener('click', async (e) => {
    const button = e.target.closest('[data-bugsnag-performance-test]');
    if (!button) return;

    e.preventDefault();

    const apiKey = button.dataset.apiKey;
    const jsPath = button.dataset.jsPath;
    const url = jsPath.startsWith('//') ? 'https:' + jsPath : jsPath;

    button.disabled = true;
    button.textContent = 'Sending...';

    try {
        const { default: BugsnagPerformance } = await import(url);
        BugsnagPerformance.start(apiKey);
        button.textContent = 'Test sent — check Bugsnag dashboard';
        button.classList.replace('btn-default', 'btn-success');
    } catch (err) {
        button.textContent = 'Failed: ' + err.message;
        button.classList.replace('btn-default', 'btn-danger');
        button.disabled = false;
    }
});
