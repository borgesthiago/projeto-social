(() => {
    const input = document.getElementById('help-search');
    if (!input) return;
    const sections = [...document.querySelectorAll('.help-section')];
    const empty = document.getElementById('help-empty');
    const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    input.addEventListener('input', () => {
        const query = normalize(input.value);
        let results = 0;
        sections.forEach(section => {
            let visible = 0;
            section.querySelectorAll('.help-card').forEach(card => {
                const match = !query || normalize(`${card.textContent} ${card.dataset.help || ''}`).includes(query);
                card.hidden = !match;
                if (match) visible++;
            });
            section.hidden = visible === 0;
            if (visible) results++;
        });
        empty.hidden = results > 0;
    });
    document.querySelectorAll('.help-topics a').forEach(link => link.addEventListener('click', () => {
        document.querySelectorAll('.help-topics a').forEach(item => item.classList.remove('active'));
        link.classList.add('active');
    }));
})();
