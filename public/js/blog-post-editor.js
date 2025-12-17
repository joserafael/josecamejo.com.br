document.addEventListener('DOMContentLoaded', function () {
    const contentElement = document.getElementById('content');
    if (contentElement) {
        const autosaveId = contentElement.getAttribute('data-autosave-id');

        new EasyMDE({
            element: contentElement,
            spellChecker: false,
            placeholder: "Escreva o conteúdo do post...",
            autosave: {
                enabled: !!autosaveId,
                uniqueId: autosaveId || "blog_post_temp",
                delay: 1000,
            },
            status: ["autosave", "lines", "words", "cursor"],
            maxHeight: "500px",
        });
    }
});
