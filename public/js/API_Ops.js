const apiOpsUrl = "/quote";   // ← points to Laravel route

async function loadQuote() {
    const quoteBox = document.getElementById("quote-box");
    quoteBox.textContent = "Loading quote...";

    try {
        // GET request, no FormData needed — matches Route::get('/quote', ...)
        const response = await fetch(apiOpsUrl, {
            method: "GET"
        });

        const result = await response.json();

        if (result.status === "success") {
            quoteBox.innerHTML = `"${result.quote}" — <strong>${result.author}</strong>`;
        } else {
            quoteBox.textContent = result.message || "Could not load quote.";
        }
    } catch (error) {
        quoteBox.textContent = "Error .Could not load quote.";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadQuote();

    const refreshBtn = document.getElementById("refresh-quote-btn");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", loadQuote);
    }
});