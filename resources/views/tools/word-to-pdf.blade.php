<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Word to PDF - PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
    };
    </script>

    <style>
    * {
        box-sizing: border-box
    }

    body {
        font-family: Inter, Arial, sans-serif
    }

    html {
        scroll-behavior: smooth;
        scrollbar-width: none
    }

    .hero {
        background: #fff8f7;
        background-image:
            linear-gradient(#e5322d08 1px, transparent 1px),
            linear-gradient(90deg, #e5322d08 1px, transparent 1px);
        background-size: 45px 45px
    }

    .drag {
        border-color: #e5322d !important;
        background: #fff5f5 !important
    }

    .fade {
        animation: fade .25s ease
    }

    @keyframes fade {
        from {
            opacity: 0;
            transform: translateY(5px)
        }

        to {
            opacity: 1;
            transform: none
        }
    }

    .loader {
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff55;
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 flex flex-col">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">

            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
                <span class="text-2xl font-black text-slate-900">Tools</span>
            </a>

            <nav class="hidden md:flex gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="hover:text-brand">All Tools</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>

            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">
                Explore Tools
            </a>

        </div>
    </header>

    <section class="hero relative overflow-hidden">
        <div class="absolute -top-24 -right-20 w-72 h-72 bg-red-100/60 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-20 w-72 h-72 bg-red-50 rounded-full blur-3xl"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-16 text-center">

            <span
                class="inline-flex rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">
                ● PDF Tool 05
            </span>

            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">
                Word to PDF <span class="text-brand">File</span>
            </h1>

            <p class="max-w-2xl mx-auto mt-4 text-slate-600 text-base sm:text-lg">
                Convert your Word documents into PDF files quickly, easily and securely.
            </p>

        </div>
    </section>

    <main class="flex-1 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

            <div class="mb-5 bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                    <div>
                        <p class="font-black text-slate-900">Free Uploads</p>
                        <p id="usageText" class="text-sm text-slate-500 mt-1">
                            You have 4 free file uploads available across all PDF tools.
                        </p>
                    </div>

                    <div class="text-right">
                        <span id="usageCount" class="text-xl font-black text-brand">0 / 4</span>
                        <p class="text-xs text-slate-400">Global usage</p>
                    </div>

                </div>
            </div>

            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-6 lg:p-8">

                <h2 class="text-xl sm:text-2xl font-black">Convert Word to PDF</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Upload your DOCX document and convert it to PDF.
                </p>

                <div id="dropZone"
                    class="mt-6 bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl p-8 sm:p-12 text-center hover:border-brand hover:bg-red-50 transition cursor-pointer">

                    <div
                        class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-brand flex items-center justify-center text-2xl font-black">
                        DOCX
                    </div>

                    <h2 class="text-xl font-bold mt-4">Upload your Word document</h2>

                    <p class="text-slate-500 mt-2 text-sm sm:text-base">
                        Drag & drop your DOCX file here or click to browse
                    </p>

                    <p class="text-xs text-slate-400 mt-2">
                        Supported format: DOCX • No application file-size limit
                    </p>

                    <input id="fileInput" type="file"
                        accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        class="hidden">

                    <button type="button" id="chooseBtn"
                        class="mt-5 bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg font-bold">
                        Choose Word File
                    </button>

                </div>

                <div id="fileBox" class="hidden fade mt-6 bg-slate-50 rounded-xl border border-slate-200 p-4">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        <div class="flex items-center gap-3 min-w-0">

                            <div
                                class="w-11 h-11 shrink-0 rounded-lg bg-red-50 text-brand flex items-center justify-center font-bold text-xs">
                                DOCX
                            </div>

                            <div class="min-w-0">
                                <p id="fileName" class="font-bold break-all text-sm"></p>
                                <p id="fileSize" class="text-sm text-slate-500 mt-1"></p>
                            </div>

                        </div>

                        <button id="deleteBtn"
                            class="self-end sm:self-auto bg-brand hover:bg-brandDark text-white px-5 py-3 rounded-lg text-sm font-bold">
                            Delete
                        </button>

                    </div>

                    <button id="convertBtn"
                        class="mt-5 w-full bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold disabled:opacity-60 disabled:cursor-not-allowed">
                        Convert to PDF
                    </button>

                    <div id="progress" class="hidden mt-5">

                        <div class="flex justify-between text-xs font-bold mb-2">
                            <span id="progressText">Processing...</span>
                            <span id="percent">0%</span>
                        </div>

                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div id="bar" class="h-full bg-brand w-0 transition-all duration-300"></div>
                        </div>

                    </div>

                    <x-payment-upgrade-card />

                    <div id="errorBox"
                        class="hidden mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    </div>

                </div>

                <div id="result"
                    class="hidden fade mt-6 bg-white rounded-2xl border border-emerald-200 p-5 sm:p-6 shadow-sm text-center">

                    <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl p-4 font-bold">
                        ✓ PDF Created Successfully
                    </div>

                    <p id="status" class="text-slate-500 mt-3 text-sm"></p>

                    <div class="grid sm:grid-cols-2 gap-3 mt-5">

                        <button id="viewBtn"
                            class="bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-lg font-bold">
                            View PDF
                        </button>

                        <a id="download" class="bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold">
                            Download PDF ↓
                        </a>

                    </div>
                </div>

            </section>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-7">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">

                <a href="{{route('home')}}" class="text-2xl font-black text-slate-900">
                    PDF<span class="text-brand">Tools</span>
                </a>

                <p class="text-sm text-slate-500">
                    © {{date('Y')}} PDFTools. All rights reserved.
                </p>

                <a href="{{route('home')}}#tools" class="text-sm font-bold text-brand hover:text-brandDark">
                    All Tools →
                </a>

            </div>
        </div>
    </footer>

    <div id="preview" class="hidden fixed inset-0 bg-black/70 z-50 p-3 sm:p-8">

        <div class="bg-white w-full h-full rounded-xl sm:rounded-2xl overflow-hidden flex flex-col">

            <div class="p-4 border-b flex justify-between items-center gap-3">
                <b>PDF Preview</b>

                <button id="closeBtn" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg font-semibold">
                    Close
                </button>
            </div>

            <iframe id="frame" class="w-full flex-1"></iframe>

        </div>
    </div>

    <script>
    const FREE_LIMIT = 4;
    let file = null,
        pdfUrl = null;

    const $ = id => document.getElementById(id);

    const input = $("fileInput"),
        zone = $("dropZone"),
        fileBox = $("fileBox"),
        fileName = $("fileName"),
        fileSize = $("fileSize"),
        result = $("result"),
        progress = $("progress"),
        progressText = $("progressText"),
        bar = $("bar"),
        percent = $("percent"),
        convertBtn = $("convertBtn"),
        download = $("download"),
        preview = $("preview"),
        frame = $("frame"),
        paymentBox = $("paymentBox"),
        errorBox = $("errorBox");

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ||
        "{{csrf_token()}}";

    /* USAGE */
    async function reserveUpload() {

        try {

            const r = await fetch("{{route('pdf.usage.reserve')}}", {
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
                data = await r.json()
            } catch (e) {}

            if (data.free_uploads_used !== undefined)
                updateUsage(data.free_uploads_used, data.remaining);

            return {
                ok: r.ok,
                status: r.status,
                ...data
            };

        } catch (e) {

            return {
                ok: false,
                status: 500,
                allowed: false,
                requires_payment: false,
                message: "Unable to check upload access. Please try again."
            };

        }
    }

    /* USAGE UI */
    function updateUsage(used, remaining) {

        if (remaining === null || remaining === undefined) {

            $("usageCount").textContent = "Unlimited";
            $("usageText").textContent =
                "Premium access active. Unlimited uploads available.";

            return;
        }

        const u = Math.min(Math.max(Number(used || 0), 0), FREE_LIMIT);
        const rem = Math.max(Number(remaining || 0), 0);

        $("usageCount").textContent = u + " / " + FREE_LIMIT;

        $("usageText").textContent = rem > 0 ?
            `You have ${rem} free file upload${rem===1?"":"s"} remaining.` :
            `You have used your ${FREE_LIMIT} free uploads.`;
    }

    /* PAYMENT */
    function showPayment() {

        paymentBox.classList.remove("hidden");

        paymentBox.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });

    }

    /* ERROR */
    function showError(message) {

        errorBox.textContent = message || "Something went wrong.";
        errorBox.classList.remove("hidden");

    }

    /* CHOOSE */
    $("chooseBtn").onclick = e => {
        e.stopPropagation();
        input.click();
    };

    zone.onclick = e => {
        if (!e.target.closest("button")) input.click();
    };

    input.onchange = e => {
        if (e.target.files[0]) loadFile(e.target.files[0]);
    };

    /* DRAG DROP */
    ["dragenter", "dragover"].forEach(type => {
        zone.addEventListener(type, e => {
            e.preventDefault();
            zone.classList.add("drag");
        });
    });

    ["dragleave", "drop"].forEach(type => {
        zone.addEventListener(type, e => {
            e.preventDefault();
            zone.classList.remove("drag");
        });
    });

    zone.ondrop = e => {
        const f = e.dataTransfer.files[0];
        if (f) loadFile(f);
    };

    /* LOAD FILE */
    function loadFile(f) {

        if (f.size === 0) {
            input.value = "";
            return showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty DOCX file.');
        }
        errorBox.classList.add("hidden");
        paymentBox.classList.add("hidden");
        result.classList.add("hidden");

        if (!/\.docx$/i.test(f.name)) {
            showError("Please select a DOCX Word file.");
            return;
        }

        file = f;

        fileName.textContent = f.name;
        fileSize.textContent =
            "Word Document • " + (f.size / 1024).toFixed(2) + " KB";

        fileBox.classList.remove("hidden");

        progress.classList.add("hidden");

        bar.style.width = "0%";
        percent.textContent = "0%";
    }

    /* CONVERT */
    async function convertPDF() {

        if (!file) {
            showError("Please select a Word file.");
            return;
        }

        if (typeof mammoth === "undefined") {
            showError("Word conversion library failed to load.");
            return;
        }

        if (typeof html2pdf === "undefined") {
            showError("PDF conversion library failed to load.");
            return;
        }

        convertBtn.disabled = true;
        convertBtn.innerHTML =
            '<span class="loader inline-block mr-2"></span> Processing...';

        progress.classList.remove("hidden");
        paymentBox.classList.add("hidden");
        errorBox.classList.add("hidden");

        let content = null;

        try {

            /* CHECK ACCESS */
            progressText.textContent = "Checking free upload...";
            bar.style.width = "5%";
            percent.textContent = "5%";

            const access = await reserveUpload();

            if (access.requires_payment ||
                access.allowed === false ||
                access.status === 402) {

                convertBtn.disabled = false;
                convertBtn.textContent = "Convert to PDF";
                showPayment();
                return;
            }

            if (!access.ok) {
                throw new Error(
                    access.message || "Upload access could not be verified."
                );
            }

            /* READ WORD */
            progressText.textContent = "Reading Word document...";
            bar.style.width = "20%";
            percent.textContent = "20%";

            const arrayBuffer = await file.arrayBuffer();

            const resultData = await mammoth.convertToHtml({
                arrayBuffer: arrayBuffer
            });

            if (!resultData.value || !resultData.value.trim()) {
                throw new Error("No readable content found in this Word file.");
            }

            /* PREPARE HTML */
            progressText.textContent = "Preparing document...";
            bar.style.width = "40%";
            percent.textContent = "40%";

            content = document.createElement("div");

            content.innerHTML = `
<div id="pdfPaper"
style="
width:794px;
min-height:1123px;
padding:60px;
background:#fff;
color:#111;
font-family:Arial,Helvetica,sans-serif;
font-size:15px;
line-height:1.6;
word-wrap:break-word;
overflow:hidden;">
${resultData.value}
</div>`;

            const paper = content.querySelector("#pdfPaper");

            /* IMAGES */
            paper.querySelectorAll("img").forEach(img => {
                img.style.maxWidth = "100%";
                img.style.height = "auto";
            });

            /* TABLES */
            paper.querySelectorAll("table").forEach(table => {
                table.style.width = "100%";
                table.style.borderCollapse = "collapse";
            });

            paper.querySelectorAll("td,th").forEach(cell => {
                cell.style.border = "1px solid #999";
                cell.style.padding = "6px";
            });

            /* PARAGRAPHS */
            paper.querySelectorAll("p").forEach(p => {
                p.style.margin = "0 0 12px 0";
            });

            /* HEADINGS */
            paper.querySelectorAll("h1,h2,h3,h4,h5,h6").forEach(h => {
                h.style.marginTop = "15px";
                h.style.marginBottom = "10px";
            });

            /* RENDER */
            content.style.position = "fixed";
            content.style.left = "-10000px";
            content.style.top = "0";
            content.style.width = "794px";
            content.style.background = "#fff";

            document.body.appendChild(content);

            await new Promise(resolve => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(resolve);
                });
            });

            /* PDF */
            progressText.textContent = "Creating PDF...";
            bar.style.width = "65%";
            percent.textContent = "65%";

            const name =
                file.name.replace(/\.[^/.]+$/, "") + ".pdf";

            const options = {
                margin: 10,
                filename: name,
                image: {
                    type: "jpeg",
                    quality: .95
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: "#fff",
                    logging: false,
                    scrollX: 0,
                    scrollY: 0
                },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: "portrait",
                    compress: true
                },
                pagebreak: {
                    mode: ["css", "legacy"]
                }
            };

            progressText.textContent = "Generating PDF file...";
            bar.style.width = "80%";
            percent.textContent = "80%";

            const blob = await html2pdf()
                .set(options)
                .from(paper)
                .outputPdf("blob");

            if (!blob || blob.size === 0)
                throw new Error("PDF file could not be generated.");

            content.remove();
            content = null;

            if (pdfUrl) URL.revokeObjectURL(pdfUrl);

            pdfUrl = URL.createObjectURL(blob);

            bar.style.width = "100%";
            percent.textContent = "100%";
            progressText.textContent = "Complete!";

            $("status").textContent =
                "Your Word document has been converted successfully.";

            download.href = pdfUrl;
            download.download = name;

            result.classList.remove("hidden");

            result.scrollIntoView({
                behavior: "smooth"
            });

        } catch (e) {

            console.error("Word to PDF:", e);

            if (content) {
                content.remove();
                content = null;
            }

            showError(
                e.message ||
                "Conversion failed. Please try another DOCX file."
            );

        } finally {

            convertBtn.disabled = false;
            convertBtn.textContent = "Convert to PDF";

        }
    }

    convertBtn.onclick = convertPDF;

    /* VIEW */
    $("viewBtn").onclick = () => {

        if (!pdfUrl) return;

        frame.src = pdfUrl;
        preview.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");

    };

    /* CLOSE */
    $("closeBtn").onclick = closePreview;

    function closePreview() {

        preview.classList.add("hidden");
        frame.src = "";
        document.body.classList.remove("overflow-hidden");

    }

    /* DELETE */
    $("deleteBtn").onclick = () => {

        closePreview();

        if (pdfUrl) URL.revokeObjectURL(pdfUrl);

        pdfUrl = null;
        file = null;
        input.value = "";

        fileBox.classList.add("hidden");
        result.classList.add("hidden");
        progress.classList.add("hidden");
        paymentBox.classList.add("hidden");
        errorBox.classList.add("hidden");

        bar.style.width = "0%";
        percent.textContent = "0%";
        progressText.textContent = "Processing...";

    };

    /* INITIAL */
    updateUsage(0, FREE_LIMIT);
    </script>

</body>

</html>