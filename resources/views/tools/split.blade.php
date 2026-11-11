<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Split PDF - PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
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
    };
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
        border-color: #e5322d !important;
        background: #fff5f5 !important
    }

    .mode.active {
        border-color: #e5322d;
        background: #fff5f5;
        color: #c9211c
    }

    .fade {
        animation: fade .25s ease
    }

    .preview {
        height: 500px
    }

    .loader {
        width: 18px;
        height: 18px;
        border: 3px solid #ffffff55;
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    .upload-icon {
        transition: .2s
    }

    #uploadZone:hover .upload-icon {
        transform: translateY(-3px)
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

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    @media(max-width:640px) {
        .preview {
            height: 400px
        }
    }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 flex flex-col">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between">
            <a href="{{route('home')}}" class="flex items-center gap-2">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
                <span class="text-2xl font-black text-slate-900">Tools</span>
            </a>
            <nav class="hidden md:flex gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="text-brand">All Tools</a>
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
                PDF Tool 02</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black">Split <span class="text-brand">PDF Files</span>
            </h1>
            <p class="max-w-2xl mx-auto mt-4 text-slate-600 text-base sm:text-lg">Extract selected pages or split your
                PDF into separate documents quickly and easily.</p>
        </div>
    </section>

    <main class="flex-1 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-6 lg:p-8">

                <div
                    class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold">Free Uploads</p>
                        <p id="usageText" class="text-xs text-slate-500 mt-1">You have 4 free file uploads available
                            across all PDF tools.</p>
                    </div>
                    <div class="text-sm font-black text-brand"><span id="usedCount">0</span> / 4</div>
                </div>

                <h2 class="text-xl sm:text-2xl font-black">Split your PDF</h2>
                <p class="text-sm text-slate-500 mt-1">Upload a PDF, choose pages and download your split files.</p>

                <div id="uploadZone"
                    class="relative mt-6 cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-red-50 hover:border-brand p-7 sm:p-12 text-center transition">
                    <input id="pdfFile" type="file" accept="application/pdf,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    <div  class="upload-icon relative z-10 mx-auto w-16 h-16 rounded-2xl bg-red-50 text-brand flex items-center justify-center font-black">
                        PDF</div>
                    <h3 class="relative z-10 mt-4 font-bold text-lg">Drop your PDF here</h3>
                    <p class="relative z-10 text-sm text-slate-500 mt-1">or click to browse your device</p>
                    <span class="relative z-10 inline-flex mt-5 bg-brand px-6 py-3 rounded-lg text-sm font-bold text-white pointer-events-none">Choose
                        PDF File</span>
                    <p class="relative z-10 text-xs text-slate-400 mt-4">PDF only • No artificial file size limit</p>
                </div>

                <div id="fileCard"
                    class="hidden fade mt-6 flex flex-col sm:flex-row sm:items-center gap-3 rounded-xl border bg-slate-50 p-4">
                    <div
                        class="w-11 h-11 rounded-lg bg-red-50 text-brand flex items-center justify-center font-bold text-xs">
                        PDF</div>
                    <div class="flex-1 min-w-0">
                        <p id="fileName" class="truncate font-bold text-sm"></p>
                        <p id="fileMeta" class="text-xs text-slate-500 mt-1"></p>
                    </div>
                    <button id="removeBtn"
                        class="px-5 py-3 rounded-lg bg-brand text-white text-sm font-bold">Delete</button>
                </div>

                <div id="workspace" class="hidden fade mt-7 grid lg:grid-cols-[1.1fr_.9fr] gap-6">

                    <div class="rounded-2xl border p-4 sm:p-5">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="font-bold text-lg">Split Settings</h3>
                                <p id="pageInfo" class="text-xs text-slate-500 mt-1"></p>
                            </div>
                            <span class="px-3 py-1 h-fit rounded-full bg-red-50 text-brand text-xs font-bold">PDF</span>
                        </div>

                        <div class="mt-5 grid sm:grid-cols-2 gap-3">
                            <button data-mode="ranges" class="mode active rounded-xl border-2 p-4 text-left"><b>Selected
                                    Pages</b><span class="block text-xs text-slate-500 mt-1">Create one PDF from
                                    selected pages.</span></button>
                            <button data-mode="each"
                                class="mode rounded-xl border-2 border-slate-200 p-4 text-left"><b>Every Page</b><span
                                    class="block text-xs text-slate-500 mt-1">Create one PDF for each page.</span></button>
                        </div>

                        <div id="rangeBox" class="mt-5">
                            <label class="text-sm font-bold">Pages / Ranges</label>
                            <p class="text-xs text-slate-500 mt-1">Examples: 1-3, 1,3,5 or 1-3,5</p>
                            <input id="pages" type="text" placeholder="e.g. 1-3,5" class="mt-3 w-full rounded-lg border px-4 py-3 focus:border-brand">
                            <div class="flex flex-wrap gap-2 mt-3">
                                <button data-example="1-3"  class="px-3 py-2 rounded-lg bg-slate-100 text-xs font-semibold">1-3</button>
                                <button data-example="1,3,5" class="px-3 py-2 rounded-lg bg-slate-100 text-xs font-semibold">1,3,5</button>
                                <button data-example="1-3,5" class="px-3 py-2 rounded-lg bg-slate-100 text-xs font-semibold">1-3,5</button>
                            </div>
                        </div>

                        <div id="notice" class="hidden mt-4 rounded-xl px-4 py-3 text-sm"></div>
                        <button id="splitBtn" class="mt-5 w-full bg-brand hover:bg-brandDark px-4 py-3 rounded-lg font-bold text-white disabled:opacity-60">Split PDF</button>

                        <x-payment-upgrade-card />
                    </div>

                    <div class="rounded-2xl border bg-slate-50 overflow-hidden">
                        <div class="px-5 py-4 border-b bg-white flex justify-between">
                            <h3 class="font-bold">PDF Preview</h3><span id="previewInfo"
                                class="text-xs text-slate-500"></span>
                        </div>
                        <iframe id="preview" class="preview w-full bg-white"></iframe>
                        <div id="previewFallback"
                            class="hidden preview w-full bg-white flex items-center justify-center p-6">
                            <div class="text-center">
                                <div
                                    class="mx-auto w-16 h-16 rounded-2xl bg-red-50 text-brand flex items-center justify-center font-black">
                                    PDF</div>
                                <h3 class="mt-4 font-bold">PDF Preview Unavailable</h3>
                                <p class="text-sm text-slate-500 mt-2">Your file is selected successfully. You can still
                                    split and download the PDF.</p>
                                <a id="openOriginal" target="_blank"
                                    class="inline-flex mt-5 bg-brand text-white px-5 py-3 rounded-lg text-sm font-bold">Open
                                    PDF</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="results" class="hidden mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <h3 class="font-bold text-emerald-800">✓ Split Files Ready</h3>
                    <p id="resultText" class="text-sm text-emerald-700 mt-1"></p>
                    <div id="downloads" class="grid sm:grid-cols-2 gap-3 mt-4"></div>
                </div>

            </section>
        </div>
    </main>

    <footer class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-7 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{route('home')}}" class="text-2xl font-black">PDF<span class="text-brand">Tools</span></a>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-sm font-bold text-brand">All Tools →</a>
        </div>
    </footer>

    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

    const $ = id => document.getElementById(id),
        csrf = document.querySelector('meta[name="csrf-token"]').content,
        LIMIT = 4;
    let file = null,
        url = null,
        pdf = null,
        pdfJsDoc = null,
        mode = "ranges";

    const size = b => b < 1024 ? b + " B" : b < 1048576 ? (b / 1024).toFixed(1) + " KB" : (b / 1048576).toFixed(1) +
        " MB";

    function notice(text, ok = false) {
        const b = $("notice");
        if (!text) {
            b.className = "hidden";
            return
        }
        b.textContent = text;
        b.className = "mt-4 rounded-xl px-4 py-3 text-sm " + (ok ? "bg-emerald-50 text-emerald-700" :
            "bg-red-50 text-red-700");
    }

    async function reserve() {
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
            return {
                ok: r.ok,
                status: r.status,
                ...await r.json()
            };
        } catch (e) {
            return {
                ok: false,
                allowed: false,
                message: "Unable to verify upload access."
            }
        }
    }

    async function loadPDF(f) {
        if (!f) return;
        if (f.size === 0) {
            $("pdfFile").value = "";
            return notice('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !/\.pdf$/i.test(f.name))
            return notice("Please select a PDF file.");

        file = f;
        if (url) URL.revokeObjectURL(url);
        url = URL.createObjectURL(f);

        $("fileName").textContent = f.name;
        $("fileMeta").textContent = "PDF • " + size(f.size);
        $("fileCard").classList.remove("hidden");
        $("workspace").classList.remove("hidden");
        $("results").classList.add("hidden");
        $("paymentBox")?.classList.add("hidden");
        $("openOriginal").href = url;
        $("preview").src = url + "#toolbar=0&navpanes=0";
        $("previewFallback").classList.add("hidden");
        $("preview").classList.remove("hidden");
        $("pageInfo").textContent = "Reading PDF...";
        $("previewInfo").textContent = "Loading...";
        pdf = null;
        pdfJsDoc = null;

        let bytes;
        try {
            bytes = new Uint8Array(await f.arrayBuffer())
        } catch (e) {
            return notice("Unable to read this file.")
        }

        try {
            pdfJsDoc = await pdfjsLib.getDocument({
                data: bytes.slice(),
                verbosity: 0,
                isEvalSupported: false,
                useSystemFonts: true
            }).promise;
        } catch (e) {
            pdfJsDoc = null
        }

        try {
            pdf = await PDFLib.PDFDocument.load(bytes, {
                ignoreEncryption: true,
                updateMetadata: false,
                throwOnInvalidObject: false
            });
        } catch (e) {
            pdf = null
        }

        const total = pdf?.getPageCount() || pdfJsDoc?.numPages || 0;

        if (!total) {
            $("pageInfo").textContent = "PDF could not be read";
            $("preview").classList.add("hidden");
            $("previewFallback").classList.remove("hidden");
            return notice(
            "This PDF cannot be processed. If it is protected, remove the password and upload again.");
        }

        $("pageInfo").textContent = total + " page(s) detected";
        $("previewInfo").textContent = total + " pages";
        $("pages").value = `1-${total}`;
        notice(pdf ? "PDF loaded successfully." : "PDF loaded in compatibility mode.", true);
    }

    $("pdfFile").onchange = e => loadPDF(e.target.files[0]);

    $("uploadZone").ondragover = e => {
        e.preventDefault();
        $("uploadZone").classList.add("drag");
    };

    $("uploadZone").ondragleave = () => $("uploadZone").classList.remove("drag");

    $("uploadZone").ondrop = e => {
        e.preventDefault();
        $("uploadZone").classList.remove("drag");
        loadPDF(e.dataTransfer.files[0]);
    };

    $("removeBtn").onclick = () => {
        file = pdf = pdfJsDoc = null;
        $("pdfFile").value = "";
        ["fileCard", "workspace", "results"].forEach(id => $(id).classList.add("hidden"));
        $("preview").src = "";
        if (url) URL.revokeObjectURL(url);
        url = null;
        notice("");
    };

    document.querySelectorAll(".mode").forEach(btn => btn.onclick = () => {
        mode = btn.dataset.mode;
        document.querySelectorAll(".mode").forEach(x => x.className =
            "mode rounded-xl border-2 border-slate-200 p-4 text-left");
        btn.className = "mode active rounded-xl border-2 p-4 text-left";
        $("rangeBox").classList.toggle("hidden", mode === "each");
    });

    document.querySelectorAll("[data-example]").forEach(btn => {
        btn.onclick = () => $("pages").value = btn.dataset.example;
    });

    function parsePages(value, total) {
        const out = [];
        for (const x of value.split(",").map(x => x.trim()).filter(Boolean)) {
            if (/^\d+$/.test(x)) {
                const n = +x;
                if (n < 1 || n > total) throw Error(`Page ${n} is outside the PDF.`);
                out.push(n - 1);
                continue;
            }
            const m = x.match(/^(\d+)\s*-\s*(\d+)$/);
            if (!m) throw Error("Invalid page format. Example: 1-3,5");
            let a = +m[1],
                b = +m[2];
            if (a > b)[a, b] = [b, a];
            if (a < 1 || b > total) throw Error("Selected page range is outside the PDF.");
            for (let i = a; i <= b; i++) out.push(i - 1);
        }
        return [...new Set(out)];
    }

    async function renderPage(doc, n, out) {
        const p = await doc.getPage(n),
            v = p.getViewport({
                scale: 1.5
            });
        const c = document.createElement("canvas");
        c.width = Math.ceil(v.width);
        c.height = Math.ceil(v.height);
        await p.render({
            canvasContext: c.getContext("2d", {
                alpha: false
            }),
            viewport: v
        }).promise;
        const img = await out.embedJpg(c.toDataURL("image/jpeg", .92));
        out.addPage([v.width, v.height]).drawImage(img, {
            x: 0,
            y: 0,
            width: v.width,
            height: v.height
        });
        c.width = c.height = 1;
    }

    function downloadCard(url, i, name) {
        const box = document.createElement("div");
        box.className = "rounded-xl border border-emerald-200 bg-white p-3 flex gap-2";
        box.innerHTML =
            `<a href="${url}" target="_blank" class="flex-1 text-center rounded-lg bg-slate-800 px-3 py-3 text-sm font-bold text-white">View PDF</a>
    <a href="${url}" download="${name.replace(/\.pdf$/i,"")}-split-${i+1}.pdf" class="flex-1 text-center rounded-lg bg-brand px-3 py-3 text-sm font-bold text-white">Download ↓</a>`;
        $("downloads").appendChild(box);
    }

    $("splitBtn").onclick = async () => {
        if (!file) return notice("Please upload a PDF first.");
        if (!pdf && !pdfJsDoc) return notice("This PDF cannot be processed.");

        const btn = $("splitBtn");
        btn.disabled = true;
        btn.innerHTML =
            '<span class="inline-flex items-center gap-2"><span class="loader"></span>Processing...</span>';

        try {
            const access = await reserve();

            if (access.requires_payment || access.allowed === false) {
                $("paymentBox")?.classList.remove("hidden");
                notice(access.message || "Payment is required to continue.");
                return;
            }

            if (!access.ok && !access.allowed) throw Error(access.message ||
                "Upload access could not be verified.");

            const total = pdf?.getPageCount() || pdfJsDoc.numPages;
            let groups = mode === "each" ? Array.from({
                length: total
            }, (_, i) => [i]) : [parsePages($("pages").value, total)];

            if (!groups[0].length) throw Error("Please select at least one page.");

            $("downloads").innerHTML = "";

            for (let i = 0; i < groups.length; i++) {
                const out = await PDFLib.PDFDocument.create();

                if (pdf) {
                    const pages = await out.copyPages(pdf, groups[i]);
                    pages.forEach(p => out.addPage(p));
                } else {
                    for (const p of groups[i]) await renderPage(pdfJsDoc, p + 1, out);
                }

                const bytes = await out.save({
                    useObjectStreams: false
                });
                const blob = new Blob([bytes], {
                    type: "application/pdf"
                });
                downloadCard(URL.createObjectURL(blob), i, file.name);
            }

            $("resultText").textContent = groups.length + " file(s) generated successfully.";
            $("results").classList.remove("hidden");
            notice("PDF split successfully.", true);
            $("results").scrollIntoView({
                behavior: "smooth"
            });
        } catch (e) {
            console.error(e);
            notice(e.message || "Unable to split PDF.");
        } finally {
            btn.disabled = false;
            btn.textContent = "Split PDF";
        }
    };
    </script>
</body>

</html>