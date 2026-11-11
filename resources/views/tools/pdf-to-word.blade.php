<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>PDF to Word - PDFTools</title>
    <meta name="description" content="Convert PDF files into editable Word documents online with PDFTools.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://unpkg.com/docx@8.5.0/build/index.umd.js"></script>
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
        border-color: #e5322d !important;
        transform: scale(1.01)
    }

    .loader {
        width: 20px;
        height: 20px;
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

    button,
    a {
        transition: .2s ease
    }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span><span
                    class="text-2xl font-black">Tools</span>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="hover:text-brand">All Tools</a>
                <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="hero relative overflow-hidden">
        <div class="absolute -top-24 -right-20 w-72 h-72 bg-red-100/60 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-20 w-72 h-72 bg-red-50 rounded-full blur-3xl"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-16 text-center">
            <span
                class="inline-flex rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">●
                PDF Tool 04</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">PDF to <span
                    class="text-brand">Word</span></h1>
            <p class="max-w-2xl mx-auto mt-4 text-slate-600 text-base sm:text-lg">Convert your PDF into an editable Word
                document quickly and easily.</p>
            <div class="flex flex-wrap justify-center gap-2 mt-6">
                <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-full text-xs font-bold">Free</span>
                <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-full text-xs font-bold">Secure</span>
                <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-full text-xs font-bold">Fast</span>
            </div>
        </div>
    </section>

    <main class="min-h-[600px] bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

            <div class="mb-5 bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="font-black text-slate-900">Free Uploads</p>
                        <p id="usageText" class="text-sm text-slate-500 mt-1">You have 4 free file uploads available
                            across all PDF tools.</p>
                    </div>
                    <div class="text-left sm:text-right"><span id="usageCount" class="text-xl font-black text-brand">0 /
                            4</span>
                        <p class="text-xs text-slate-400">Global usage</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-6 lg:p-8">

                <div id="dropZone"
                    class="border-2 border-dashed border-slate-300 rounded-2xl p-7 sm:p-12 lg:p-14 text-center cursor-pointer hover:bg-red-50 hover:border-brand transition">
                    <input id="fileInput" type="file" accept=".pdf,application/pdf" class="hidden">
                    <div
                        class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-brand flex items-center justify-center font-black">
                        PDF</div>
                    <h2 class="text-xl sm:text-2xl font-black mt-5">Upload PDF</h2>
                    <p class="text-slate-500 mt-2 text-sm sm:text-base">Drag & drop your PDF here or choose a file.</p>
                    <button id="browseBtn" type="button"
                        class="mt-6 bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg font-bold">Choose
                        PDF</button>
                    <p class="text-xs text-slate-400 mt-4">Supported format: PDF • No frontend MB upload limit</p>
                </div>

                <div id="fileBox" class="hidden mt-6">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 border rounded-xl p-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-11 h-11 shrink-0 rounded-lg bg-red-50 text-brand flex items-center justify-center font-bold text-xs">
                                PDF</div>
                            <div class="min-w-0">
                                <p id="fileName" class="font-bold truncate"></p>
                                <p id="fileSize" class="text-sm text-slate-500"></p>
                            </div>
                        </div>
                        <button id="deleteBtn" type="button"
                            class="text-brand hover:text-brandDark font-bold text-sm self-end sm:self-auto">Delete</button>
                    </div>

                    <button id="convertBtn" type="button"
                        class="mt-6 w-full sm:w-auto bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg font-bold disabled:opacity-60">
                        Generate Word File
                    </button>

                    <div id="progressBox" class="hidden mt-6">
                        <div class="flex justify-between text-sm mb-2"><span id="progressText">Starting...</span><span
                                id="percent">0%</span></div>
                        <div class="w-full bg-slate-200 h-3 rounded-full overflow-hidden">
                            <div id="progressBar" class="bg-brand h-full transition-all" style="width:0%"></div>
                        </div>
                    </div>

                    <x-payment-upgrade-card />

                    <div id="errorBox"
                        class="hidden mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm"></div>
                </div>

                <div id="resultBox" class="hidden mt-7 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:p-6">
                    <div class="text-center">
                        <div
                            class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl">
                            ✓</div>
                        <h2 class="text-xl font-black text-emerald-800 mt-3">Word File Generated Successfully</h2>
                        <p id="stats" class="text-emerald-700 mt-2 text-sm"></p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-6">
                        <button id="viewBtn" type="button"
                            class="bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-lg font-bold">View
                            Word</button>
                        <a id="downloadBtn"
                            class="bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold text-center">Download
                            Word</a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 bg-black/70 z-[60] p-3 sm:p-6">
        <div class="bg-white rounded-2xl max-w-5xl mx-auto h-full flex flex-col overflow-hidden">
            <div class="border-b p-3 sm:p-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                <h2 class="font-bold text-lg">Word Preview</h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button id="viewerDownload" type="button"
                        class="flex-1 sm:flex-none bg-brand hover:bg-brandDark text-white px-4 py-2.5 rounded-lg font-bold">Download</button>
                    <button id="closeBtn" type="button"
                        class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-lg font-semibold">Close</button>
                </div>
            </div>
            <div class="flex-1 overflow-auto bg-slate-100 p-3 sm:p-5">
                <div id="paper" class="bg-white max-w-3xl mx-auto min-h-full p-6 sm:p-10 shadow"></div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 py-7 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <a href="{{route('home')}}" class="text-2xl font-black text-slate-900">PDF<span
                    class="text-brand">Tools</span></a>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-sm font-bold text-brand">All Tools →</a>
        </div>
    </footer>

    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    const $ = id => document.getElementById(id);
    let file = null,
        outputUrl = null,
        preview = "";
    const input = $("fileInput"),
        drop = $("dropZone");
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "{{csrf_token()}}";

    function updateUsage(used, remaining) {
        if (remaining == null) {
            $("usageCount").textContent = "Unlimited";
            $("usageText").textContent = "Premium access active. Unlimited uploads available.";
            return
        }
        used = Math.min(Number(used || 0), 4);
        $("usageCount").textContent = `${used} / 4`;
        $("usageText").textContent = remaining > 0 ?
            `You have ${remaining} free file upload${remaining==1?"":"s"} remaining across all PDF tools.` :
            "You have used your 4 free uploads across all PDF tools.";
    }

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
            let d = {};
            try {
                d = await r.json()
            } catch (e) {}
            if (d.premium) updateUsage(4, null);
            else updateUsage(d.free_uploads_used ?? 0, d.remaining ?? Math.max(0, 4 - (d.free_uploads_used ?? 0)));
            return {
                ok: r.ok,
                status: r.status,
                ...d
            };
        } catch (e) {
            return {
                ok: false,
                status: 500,
                message: "Unable to check upload access. Please try again."
            }
        }
    }

    function error(msg) {
        $("errorBox").textContent = msg;
        $("errorBox").classList.remove("hidden");
        $("errorBox").scrollIntoView({
            behavior: "smooth",
            block: "center"
        })
    }

    function size(b) {
        if (b < 1024) return b + " Bytes";
        if (b < 1048576) return (b / 1024).toFixed(2) + " KB";
        if (b < 1073741824) return (b / 1048576).toFixed(2) + " MB";
        return (b / 1073741824).toFixed(2) + " GB"
    }

    function progress(v, t) {
        v = Math.max(0, Math.min(100, Number(v)));
        $("progressBar").style.width = v + "%";
        $("percent").textContent = v + "%";
        $("progressText").textContent = t
    }

    $("browseBtn").onclick = e => {
        e.stopPropagation();
        input.click()
    };
    drop.onclick = e => {
        if (!e.target.closest("button")) input.click()
    };
    input.onchange = () => {
        if (input.files.length) loadFile(input.files[0])
    };
    drop.ondragover = e => {
        e.preventDefault();
        drop.classList.add("drag")
    };
    drop.ondragleave = () => drop.classList.remove("drag");
    drop.ondrop = e => {
        e.preventDefault();
        drop.classList.remove("drag");
        if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0])
    };

    function loadFile(f) {
        $("errorBox").classList.add("hidden");
        if (f.size === 0) {
            input.value = "";
            return error('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !f.name.toLowerCase().endsWith(".pdf")) return error(
            "Please select a valid PDF file.");
        file = f;
        $("fileName").textContent = f.name;
        $("fileSize").textContent = size(f.size);
        $("fileBox").classList.remove("hidden");
        $("resultBox").classList.add("hidden");
        $("progressBox").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        progress(0, "Starting...");
    }

    function showPayment() {
        $("progressBox").classList.add("hidden");
        $("paymentBox").classList.remove("hidden");
        $("paymentBox").scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }

    $("convertBtn").onclick = async () => {
        if (!file) return error("Please select a PDF first.");
        if (!window.docx) return error("DOCX library failed to load. Please refresh the page.");
        const btn = $("convertBtn");
        btn.disabled = true;
        btn.innerHTML = '<span class="loader inline-block mr-2"></span> Converting...';
        $("progressBox").classList.remove("hidden");
        $("errorBox").classList.add("hidden");
        $("paymentBox").classList.add("hidden");

        try {
            progress(3, "Checking free upload...");
            const access = await reserveUpload();
            if (!access.ok || access.allowed === false) {
                btn.disabled = false;
                btn.textContent = "Generate Word File";
                if (access.requires_payment || access.status === 402) {
                    showPayment();
                    return
                }
                throw new Error(access.message || "Upload is not allowed.");
            }

            progress(5, "Reading PDF...");
            const buffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({
                data: new Uint8Array(buffer)
            }).promise;
            let paragraphs = [],
                html = "";

            for (let i = 1; i <= pdf.numPages; i++) {
                progress(10 + Math.round(i / pdf.numPages * 65), `Reading page ${i} of ${pdf.numPages}...`);
                const page = await pdf.getPage(i),
                    content = await page.getTextContent();
                const text = content.items.map(x => x.str).join(" ").replace(/\s+/g, " ").trim();
                if (text) {
                    paragraphs.push(text);
                    html += `<p class="mb-5">${escapeHTML(text)}</p>`
                }
            }

            if (!paragraphs.length) throw new Error("No selectable text found. This may be a scanned PDF.");

            progress(82, "Creating Word document...");
            const children = paragraphs.map(t => new window.docx.Paragraph({
                children: [new window.docx.TextRun({
                    text: t,
                    size: 22
                })],
                spacing: {
                    after: 250
                }
            }));
            const doc = new window.docx.Document({
                sections: [{
                    properties: {},
                    children
                }]
            });

            progress(90, "Generating Word file...");
            const blob = await window.docx.Packer.toBlob(doc);
            if (outputUrl) URL.revokeObjectURL(outputUrl);
            outputUrl = URL.createObjectURL(blob);
            preview = html;

            const filename = file.name.replace(/\.pdf$/i, "") + ".docx";
            $("downloadBtn").href = outputUrl;
            $("downloadBtn").download = filename;
            progress(100, "Complete!");
            $("stats").textContent = `${pdf.numPages} page(s) converted successfully.`;
            $("resultBox").classList.remove("hidden");
            $("resultBox").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        } catch (e) {
            console.error(e);
            error(e.message || "Conversion failed. Please try another PDF.")
        } finally {
            btn.disabled = false;
            btn.textContent = "Generate Word File"
        }
    };

    function escapeHTML(t) {
        const d = document.createElement("div");
        d.textContent = t;
        return d.innerHTML
    }

    $("viewBtn").onclick = () => {
        if (!outputUrl) return;
        $("paper").innerHTML = preview;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    };

    $("viewerDownload").onclick = () => {
        if (!outputUrl) return;
        const a = document.createElement("a");
        a.href = outputUrl;
        a.download = file.name.replace(/\.pdf$/i, "") + ".docx";
        document.body.appendChild(a);
        a.click();
        a.remove();
    };

    function closeViewer() {
        $("viewer").classList.add("hidden");
        document.body.classList.remove("overflow-hidden")
    }
    $("closeBtn").onclick = closeViewer;
    $("viewer").onclick = e => {
        if (e.target === $("viewer")) closeViewer()
    };

    $("deleteBtn").onclick = () => {
        if (outputUrl) URL.revokeObjectURL(outputUrl);
        file = null;
        outputUrl = null;
        preview = "";
        input.value = "";
        ["fileBox", "resultBox", "progressBox", "paymentBox", "errorBox"].forEach(id => $(id).classList.add(
            "hidden"));
        progress(0, "Starting...");
    };

    updateUsage(0, 4);
    </script>
</body>

</html>