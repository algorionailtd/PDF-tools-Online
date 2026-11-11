<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>PDF to JPG - PDFTools</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: '#e5322d',
                brandDark: '#c9211c'
            }
        }
    }
}
</script>

<style>
* {
    box-sizing: border-box
}

html {
    scroll-behavior: smooth;
    scrollbar-width: none
}

body {
    font-family: Inter, Arial, sans-serif
}

.hero {
    background: #fff8f7;
    background-image: linear-gradient(#e5322d08 1px, transparent 1px), linear-gradient(90deg, #e5322d08 1px, transparent 1px);
    background-size: 45px 45px
}

.drag {
    background: #fff5f5 !important;
    border-color: #e5322d !important
}

.loader {
    width: 18px;
    height: 18px;
    border: 3px solid #ffffff55;
    border-top-color: white;
    border-radius: 50%;
    animation: spin .7s linear infinite;
    display: inline-block;
    vertical-align: middle
}

@keyframes spin {
    to {
        transform: rotate(360deg)
    }
}
</style>

</head>

<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
        <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0">
            <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
            <span class="text-2xl font-black">Tools</span>
        </a>
        <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
            <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
            <a href="{{route('home')}}#tools" class="text-brand">All Tools</a>
            <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
            <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
        </nav>
        <a href="{{route('home')}}#tools"
            class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
            Tools</a>
    </div>
</header>

<section class="hero border-b border-red-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-14 text-center">
        <span
            class="inline-flex rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">🖼️
            PDF Tool 06</span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black mt-5">PDF to <span class="text-brand">JPG</span></h1>
        <p class="text-sm sm:text-base text-slate-600 mt-4 max-w-2xl mx-auto">Convert every PDF page into
            high-quality JPG images quickly and easily.</p>
    </div>
</section>

<main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

    <div class="mb-6 rounded-2xl bg-blue-50 border border-blue-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="font-bold">Free Uploads</h3>
                <p id="usageText" class="text-sm text-slate-500 mt-1">You have 4 free file uploads available across all PDF tools.</p>
            </div>
            <div class="text-right">
                <span id="usageCount" class="text-2xl font-black text-brand">0 / 4</span>
                <p class="text-xs text-slate-400">Global uploads</p>
            </div>
        </div>
    </div>

    <div id="dropZone"
      class="upload-card border-2 border-dashed border-slate-300 hover:border-red-300 rounded-3xl p-8 sm:p-12 text-center hover:bg-red-50 bg-white">
       
        <input id="pdfInput" type="file" accept=".pdf,application/pdf" class="hidden">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-3xl">📄</div>
        <h2 class="text-xl sm:text-2xl font-bold mt-5">Upload your PDF</h2>
        <p class="text-sm sm:text-base text-slate-500 mt-2">Drag & drop your PDF here or choose a file.</p>
        <button id="browseBtn" type="button"
            class="mt-5 bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg shadow-sm">Choose PDF
            File</button>
        <p class="text-xs text-slate-400 mt-4">PDF • No frontend MB upload limit</p>
    </div>

    <div id="errorBox"
        class="hidden mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold"></div>

    <div id="fileBox" class="hidden mt-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="w-12 h-12 rounded-xl bg-red-100 text-brand flex items-center justify-center font-black text-xs shrink-0">
                    PDF</div>
                <div class="min-w-0">
                    <p id="fileName" class="font-bold truncate"></p>
                    <p id="fileSize" class="text-sm text-slate-500 mt-1"></p>
                </div>
            </div>
            <button id="removeBtn" type="button"
                class="text-brand hover:text-brandDark font-bold text-sm shrink-0">Delete</button>
        </div>
    </div>

    <div id="settings" class="hidden mt-6 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
        <h3 class="text-xl font-black">Conversion Settings</h3>
        <p class="text-sm text-slate-500 mt-1">Choose image quality and resolution.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
            <div>
                <label class="block text-sm font-semibold mb-2">Image Quality</label>
                <select id="quality"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-brand">
                    <option value="0.95">High Quality</option>
                    <option value="0.85">Medium Quality</option>
                    <option value="0.70">Low Quality</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Resolution</label>
                <select id="dpi"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-brand">
                    <option value="72">72 DPI</option>
                    <option value="150" selected>150 DPI</option>
                    <option value="200">200 DPI</option>
                    <option value="300">300 DPI</option>
                </select>
            </div>
        </div>

        <button id="generateBtn" type="button"
            class="mt-6 w-full bg-brand hover:bg-brandDark text-white font-bold py-3 rounded-xl">Convert All Pages
            to JPG →</button>

        <x-payment-upgrade-card />
    </div>

    <div id="progressBox" class="hidden mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex justify-between text-sm mb-2">
            <span id="progressText">Converting...</span><b id="progressPercent">0%</b>
        </div>
        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
            <div id="progressBar" class="h-full bg-brand rounded-full transition-all" style="width:0%"></div>
        </div>
    </div>

    <div id="result" class="hidden mt-6 rounded-3xl border border-green-200 bg-white overflow-hidden">
        <div class="bg-green-50 border-b border-green-100 p-5">
            <h3 class="font-bold text-green-800 text-lg">✓ Conversion Complete</h3>
            <p id="pageCount" class="text-sm text-green-700 mt-1"></p>
        </div>
        <div class="p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold">Converted JPG Images</h3>
                    <p class="text-sm text-slate-500">Preview or download individual pages.</p>
                </div>
                <button id="downloadAllBtn" type="button"
                    class="bg-brand hover:bg-brandDark text-white px-5 py-3 rounded-lg font-bold">⬇ Download All
                    JPG</button>
            </div>
            <div id="pagesContainer" class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6"></div>
        </div>
    </div>
</main>

<div id="viewer" class="hidden fixed inset-0 z-[70] bg-black/90 p-2 sm:p-5">
    <div class="w-full h-full max-w-6xl mx-auto bg-white rounded-2xl overflow-hidden flex flex-col">
        <div class="h-14 shrink-0 px-4 border-b border-slate-200 flex items-center justify-between">
            <b class="text-sm sm:text-base">JPG Preview</b>
            <button id="closeViewer" type="button"
                class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg font-bold">✕ Close</button>
        </div>
        <div class="flex-1 bg-slate-900 flex items-center justify-center p-3">
            <img id="viewerImage" class="max-w-full max-h-full object-contain rounded-lg">
        </div>
    </div>
</div>

<footer class="bg-white border-t border-slate-100 mt-10">
    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 py-7 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
        <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
        <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
        <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
    </div>
</footer>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const $ = id => document.getElementById(id);
let selectedFile = null,
    images = [];
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "{{csrf_token()}}";

async function reserveUpload() {
    const response = await fetch("{{route('pdf.usage.reserve')}}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf
        },
        body: JSON.stringify({
            file_count: 1
        })
    });
    let data = {};
    try {
        data = await response.json()
    } catch (e) {
        data = {
            message: "Invalid server response."
        }
    }

    if (data.premium) {
        $("usageCount").textContent = "Unlimited";
        $("usageText").textContent = "Premium access active. Unlimited uploads available.";
    } else {
        const used = Number(data.free_uploads_used ?? 0);
        const remaining = Number(data.remaining ?? Math.max(0, 4 - used));
        $("usageCount").textContent = `${Math.min(used,4)} / 4`;
        $("usageText").textContent = remaining > 0 ?
            `${remaining} free upload(s) remaining across all PDF tools.` :
            "Your 4 free uploads across all PDF tools have been used.";
    }
    return {
        ok: response.ok,
        status: response.status,
        ...data
    };
}

function showError(text) {
    $("errorBox").textContent = text;
    $("errorBox").classList.remove("hidden")
}

function hideError() {
    $("errorBox").classList.add("hidden")
}

$("browseBtn").onclick = e => {
    e.preventDefault();
    e.stopPropagation();
    $("pdfInput").click()
};
$("dropZone").onclick = e => {
    if (!e.target.closest("button")) $("pdfInput").click()
};
$("pdfInput").onchange = e => {
    if (e.target.files.length) handleFile(e.target.files[0])
};

$("dropZone").ondragover = e => {
    e.preventDefault();
    $("dropZone").classList.add("drag")
};
$("dropZone").ondragleave = () => $("dropZone").classList.remove("drag");
$("dropZone").ondrop = e => {
    e.preventDefault();
    $("dropZone").classList.remove("drag");
    if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
};

function handleFile(file) {
    hideError();
    if (file.size === 0) {
        $("pdfInput").value = "";
        return showError('"' + file.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
    }
    if (file.type !== "application/pdf" && !file.name.toLowerCase().endsWith(".pdf"))
        return showError("Please select a valid PDF file.");

    selectedFile = file;
    $("fileName").textContent = file.name;
    $("fileSize").textContent = formatSize(file.size);
    $("dropZone").classList.add("hidden");
    $("fileBox").classList.remove("hidden");
    $("settings").classList.remove("hidden");
    $("result").classList.add("hidden");
    $("progressBox").classList.add("hidden");
    $("paymentBox").classList.add("hidden");
    clearImages();
}

$("removeBtn").onclick = () => {
    selectedFile = null;
    $("pdfInput").value = "";
    clearImages();
    $("dropZone").classList.remove("hidden");
    $("fileBox").classList.add("hidden");
    $("settings").classList.add("hidden");
    $("progressBox").classList.add("hidden");
    $("result").classList.add("hidden");
    $("paymentBox").classList.add("hidden");
    $("progressBar").style.width = "0%";
    $("progressPercent").textContent = "0%";
    hideError();
};

function clearImages() {
    images.forEach(i => i.url && URL.revokeObjectURL(i.url));
    images = [];
    $("pagesContainer").innerHTML = "";
}

$("generateBtn").onclick = async () => {
    if (!selectedFile) return showError("Please select a PDF first.");

    const button = $("generateBtn");
    let pdf = null;
    button.disabled = true;
    button.innerHTML = '<span class="loader mr-2"></span> Checking...';
    hideError();
    $("paymentBox").classList.add("hidden");
    $("progressBox").classList.remove("hidden");
    $("result").classList.add("hidden");
    clearImages();

    try {
        $("progressText").textContent = "Validating PDF...";
        $("progressBar").style.width = "5%";
        $("progressPercent").textContent = "5%";

        const buffer = new Uint8Array(await selectedFile.arrayBuffer());
        try {
            pdf = await pdfjsLib.getDocument({
                data: buffer.slice(),
                stopAtErrors: false,
                disableAutoFetch: false,
                disableStream: false,
                isEvalSupported: false,
                useSystemFonts: true
            }).promise;
        } catch (error) {
            if (error.name === "PasswordException") {
                throw new Error('"' + selectedFile.name + '" is password-protected. Remove its password before converting.');
            }
            if (error.name === "InvalidPDFException" || /No PDF header|Invalid PDF structure/i.test(error.message || "")) {
                throw new Error('"' + selectedFile.name + '" is not a valid PDF or is damaged. Re-download or export the original PDF.');
            }
            throw error;
        }

        if (!pdf.numPages) throw new Error("The PDF has no pages.");

        $("progressText").textContent = "Checking free upload...";
        const access = await reserveUpload();

        if (!access.ok || access.allowed === false) {
            if (access.requires_payment || access.status === 402) {
                $("progressBox").classList.add("hidden");
                $("paymentBox").classList.remove("hidden");
                $("paymentBox").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                return;
            }
            throw new Error(access.message || "Upload is not allowed.");
        }

        if (access.premium) {
            $("usageCount").textContent = "Unlimited";
            $("usageText").textContent = "Premium access active. Unlimited uploads available.";
        } else if (typeof access.free_uploads_used !== "undefined") {
            const used = Number(access.free_uploads_used);
            const remaining = Number(access.remaining ?? Math.max(0, 4 - used));
            $("usageCount").textContent = `${Math.min(used, 4)} / 4`;
            $("usageText").textContent = remaining > 0 ?
                `${remaining} free upload(s) remaining across all PDF tools.` :
                "Your 4 free uploads across all PDF tools have been used.";
        }

        button.innerHTML = '<span class="loader mr-2"></span> Converting...';

        for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
            $("progressText").textContent = `Converting page ${pageNumber} of ${pdf.numPages}...`;

            const percent = Math.round((pageNumber / pdf.numPages) * 95) + 5;
            $("progressBar").style.width = Math.min(percent, 100) + "%";
            $("progressPercent").textContent = Math.min(percent, 100) + "%";

            const page = await pdf.getPage(pageNumber);
            const scale = Number($("dpi").value) / 72;
            const viewport = page.getViewport({
                scale
            });
            const canvas = document.createElement("canvas");

            canvas.width = Math.ceil(viewport.width);
            canvas.height = Math.ceil(viewport.height);

            const context = canvas.getContext("2d", {
                alpha: false
            });
            context.fillStyle = "white";
            context.fillRect(0, 0, canvas.width, canvas.height);

            await page.render({
                canvasContext: context,
                viewport
            }).promise;

            const blob = await new Promise(resolve => canvas.toBlob(resolve, "image/jpeg", Number($(
                "quality").value)));
            if (!blob) throw new Error(`Could not create JPG for page ${pageNumber}.`);

            const image = {
                blob,
                url: URL.createObjectURL(blob),
                page: pageNumber
            };
            images.push(image);
            createCard(image);
        }

        $("progressBar").style.width = "100%";
        $("progressPercent").textContent = "100%";
        $("progressText").textContent = "Conversion complete.";
        $("pageCount").textContent =
            `${pdf.numPages} page${pdf.numPages===1?"":"s"} converted successfully.`;
        $("result").classList.remove("hidden");
        $("result").scrollIntoView({
            behavior: "smooth",
            block: "center"
        });

    } catch (error) {
        console.error(error);
        showError(error.message || "Unable to convert this PDF. Please make sure the PDF is valid.");
    } finally {
        if (pdf) await pdf.destroy();
        button.disabled = false;
        button.textContent = "Convert All Pages to JPG →";
    }
};

function createCard(image) {
    const card = document.createElement("div");
    card.className = "border border-slate-200 rounded-2xl p-4 bg-white shadow-sm";
    card.innerHTML = `

<div class="flex items-center justify-between gap-3 mb-3">
<h4 class="font-bold">Page ${image.page}</h4>
<span class="text-xs font-bold bg-red-50 text-brand px-2.5 py-1 rounded-full">JPG</span>
</div>
<div class="bg-slate-100 border rounded-xl p-2">
<img src="${image.url}" class="w-full max-h-[420px] object-contain rounded-lg">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
<button type="button" class="previewBtn bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold">👁 View</button>
<button type="button" class="downloadBtn bg-brand hover:bg-brandDark text-white py-3 rounded-xl font-bold">⬇ Download</button>
</div>`;

    card.querySelector(".previewBtn").onclick = () => openViewer(image.url);
    card.querySelector(".downloadBtn").onclick = () => download(image.url, `page-${image.page}.jpg`);
    $("pagesContainer").appendChild(card);
}

function openViewer(url) {
    $("viewerImage").src = url;
    $("viewer").classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
}

$("closeViewer").onclick = () => {
    $("viewer").classList.add("hidden");
    $("viewerImage").src = "";
    document.body.classList.remove("overflow-hidden");
};

function download(url, name) {
    const a = document.createElement("a");
    a.href = url;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    a.remove();
}

$("downloadAllBtn").onclick = async () => {
    if (!images.length) return showError("Please convert the PDF first.");

    const button = $("downloadAllBtn");
    button.disabled = true;
    button.textContent = "Downloading...";

    for (const image of images) {
        download(image.url, `page-${image.page}.jpg`);
        await new Promise(resolve => setTimeout(resolve, 250));
    }

    button.disabled = false;
    button.textContent = "⬇ Download All JPG";
};

function formatSize(bytes) {
    if (bytes < 1024) return bytes + " Bytes";
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + " KB";
    if (bytes < 1073741824) return (bytes / 1048576).toFixed(2) + " MB";
    return (bytes / 1073741824).toFixed(2) + " GB";
}

window.addEventListener("beforeunload", () => {
    images.forEach(i => i.url && URL.revokeObjectURL(i.url));
});
</script>

</body>

</html>