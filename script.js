document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('nameInput');
    const canvas = document.getElementById('cardCanvas');
    const ctx = canvas.getContext('2d');
    const downloadPngPostBtn = document.getElementById('downloadPngPost');
    const templateThumbs = document.querySelectorAll('.template-thumb');

    let currentTemplate = new Image();
    let selectedTemplateSrc = 'templates/template1.jpg'; // Default template

    function loadTemplate(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = (err) => reject(err);
            img.src = src;
        });
    }

    async function drawCard() {
        if (!currentTemplate.src) { // Ensure template is loaded at least once
            try {
                currentTemplate = await loadTemplate(selectedTemplateSrc);
            } catch (error) {
                console.error("Error loading template:", error);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'red';
                ctx.font = '20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Error loading template', canvas.width / 2, canvas.height / 2);
                return;
            }
        }

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw background template
        // Ensure template maintains aspect ratio and covers canvas
        // For simplicity, we'll just draw it to fit the canvas (1080x1080)
        ctx.drawImage(currentTemplate, 0, 0, canvas.width, canvas.height);

        // Text properties (This is where "AI" would be more sophisticated)
        const text = nameInput.value || "Your Name";
        let fontSize = 80; // Start with a base font size
        ctx.font = `bold ${fontSize}px Arial`; // Example font
        ctx.fillStyle = 'white'; // Example color
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        // Simple text shadow for better readability
        ctx.shadowColor = 'rgba(0,0,0,0.7)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetX = 2;
        ctx.shadowOffsetY = 2;

        // Basic responsive sizing (very rudimentary)
        let textWidth = ctx.measureText(text).width;
        const padding = 50; // Padding from canvas edges
        while (textWidth > canvas.width - 2 * padding && fontSize > 20) {
            fontSize -= 5;
            ctx.font = `bold ${fontSize}px Arial`;
            textWidth = ctx.measureText(text).width;
        }

        // Draw text (centered for this example)
        ctx.fillText(text, canvas.width / 2, canvas.height / 2);

        // Reset shadow for other drawings if any
        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;
    }

    // Event Listeners
    nameInput.addEventListener('input', drawCard);

    templateThumbs.forEach(thumb => {
        thumb.addEventListener('click', async () => {
            document.querySelector('.template-thumb.selected')?.classList.remove('selected');
            thumb.classList.add('selected');
            selectedTemplateSrc = thumb.dataset.templateSrc;
            try {
                currentTemplate = await loadTemplate(selectedTemplateSrc);
                drawCard(); // Redraw with new template
            } catch (error) {
                console.error("Error loading new template:", error);
            }
        });
    });

    downloadPngPostBtn.addEventListener('click', () => {
        // Ensure canvas is up-to-date
        drawCard();

        const dataURL = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'instagram_card_post.png';
        link.href = dataURL;
        document.body.appendChild(link); // Required for Firefox
        link.click();
        document.body.removeChild(link);
    });

    // Initial draw
    loadTemplate(selectedTemplateSrc).then(img => {
        currentTemplate = img;
        // Select the first template visually
        if (templateThumbs.length > 0) {
            templateThumbs[0].classList.add('selected');
        }
        drawCard();
    }).catch(err => console.error("Initial template load failed:", err));
});
