<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Compress PDF - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    body {
        font-family: Inter, Arial, sans-serif;
        background: #fff;
        color: #111827
    }
          html {
        scrollbar-width: none
    }

    .hero {
        background: linear-gradient(rgba(229, 50, 45, .045) 1px, transparent 1px), linear-gradient(90deg, rgba(229, 50, 45, .045) 1px, transparent 1px);
        background-size: 32px 32px
    }

    .upload-card {
        transition: .2s ease
    }

    .upload-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, .07)
    }

    .progress-bar {
        transition: width .3s ease
    }
    </style>
</head>

<body>

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="h-16 flex items-center justify-between">
                <a href="{{route('home')}}" class="flex items-center gap-2">
                    <span
                        class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-bold">PDF</span>
                    <div>
                        <div class="font-extrabold text-lg leading-none"><span class="text-brand">PDF</span>Tools</div>
                        <div class="text-[10px] text-gray-500">Every PDF tool</div>
                    </div>
                </a>
                <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                    <a href="{{route('home')}}" class="hover:text-brand">Home</a>
                    <a href="{{route('home')}}#tools" class="text-brand">All Tools</a>
                    <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                    <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
                </nav>
                <a href="{{route('home')}}#tools"
                    class="hidden sm:inline-flex bg-brand text-white px-5 py-2.5 rounded-xl font-bold hover:bg-brandDark">Explore
                    Tools</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="max-w-4xl mx-auto px-4 py-14 sm:py-20 text-center">
            <div class="inline-flex items-center gap-2 bg-red-50 text-brand px-4 py-2 rounded-full text-sm font-bold">
                🗜️ PDF Tool 03</div>
            <h1 class="mt-6 text-4xl sm:text-5xl font-black tracking-tight">Compress <span class="text-brand">PDF</span>
            </h1>
            <p class="mt-5 text-gray-600 max-w-2xl mx-auto text-base sm:text-lg">Reduce your PDF file size quickly while
                keeping your document easy to share and store.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 pb-20">

        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-extrabold text-gray-900">Free PDF Uploads</h2>
                    <p id="usageText" class="text-sm text-gray-600 mt-1">You have 4 free file uploads available across
                        all PDF tools.</p>
                </div>
                <div class="text-center bg-white border rounded-xl px-5 py-3">
                    <div id="usedCount" class="text-2xl font-black text-brand">0 / 4</div>
                    <div class="text-xs text-gray-500">Files Used</div>
                </div>
            </div>
        </div>

        <div id="uploadCard"
            class="upload-card border-2 border-dashed border-slate-300 hover:border-red-300 rounded-3xl p-8 sm:p-12 text-center hover:bg-red-50 bg-white">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-3xl">🗜️</div>
            <h2 class="mt-5 text-xl font-extrabold">Upload your PDF</h2>
            <p class="mt-2 text-gray-500">Drag & drop your PDF here or choose a file</p>
            <input id="input" type="file" accept="application/pdf,.pdf" class="hidden">
            <button id="chooseBtn" type="button"
                class="mt-6 bg-brand text-white px-7 py-3 rounded-xl font-bold hover:bg-brandDark">Choose PDF</button>
            <p class="mt-3 text-xs text-gray-400">PDF • No frontend MB upload limit</p>
        </div>

        <div id="box" class="hidden mt-6 bg-white border rounded-2xl p-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">📄</div>
                    <div class="min-w-0">
                        <div id="name" class="font-bold truncate"></div>
                        <div id="size" class="text-sm text-gray-500"></div>
                    </div>
                </div>
                <button id="deleteBtn" class="text-sm font-bold text-red-600 hover:text-red-700">Delete</button>
            </div>
        </div>

        <div id="options" class="hidden mt-6 bg-white border rounded-2xl p-6">
            <h3 class="font-extrabold text-lg">Compression Settings</h3>
            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-bold mb-2">Target Size</label><input id="target" type="number"
                        value="500" min="1" class="w-full border rounded-xl px-4 py-3 outline-none focus:border-brand">
                </div>
                <div><label class="block text-sm font-bold mb-2">Unit</label><select id="unit"
                        class="w-full border rounded-xl px-4 py-3 outline-none focus:border-brand">
                        <option value="KB">KB</option>
                        <option value="MB">MB</option>
                    </select></div>
            </div>
            <button id="compress"
                class="mt-5 w-full bg-brand text-white py-3.5 rounded-xl font-bold hover:bg-brandDark">Compress
                PDF</button>
            <div id="progressBox" class="hidden mt-6">
                <div class="flex justify-between text-sm font-bold mb-2"><span>Compressing...</span><span
                        id="pct">0%</span></div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div id="bar" class="progress-bar h-full bg-brand" style="width:0%"></div>
                </div>
                <p id="ptext" class="text-xs text-gray-500 mt-2 text-center">Preparing PDF...</p>
            </div>
            <div id="errorBox" class="hidden mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 p-4 text-sm">
            </div>
        </div>

        <x-payment-upgrade-card />

        <div id="result" class="hidden mt-6 bg-white border rounded-2xl p-6">
            <div class="rounded-xl bg-green-50 border border-green-200 p-5">
                <div class="font-extrabold text-green-700">✓ PDF Compressed Successfully</div>
                <div id="stats" class="text-sm text-green-700 mt-2"></div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3 mt-5">
                <button id="viewBtn" class="border rounded-xl py-3 font-bold hover:bg-gray-50">View PDF</button>
                <a id="download"
                    class="bg-brand text-white rounded-xl py-3 font-bold text-center hover:bg-brandDark">Download
                    PDF</a>
            </div>
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 z-50 bg-black/80 p-4 sm:p-8">
        <div class="relative bg-white rounded-2xl h-full overflow-hidden">
            <button id="closeViewer"
                class="absolute right-4 top-4 z-10 bg-black text-white w-10 h-10 rounded-full font-bold">×</button>
            <iframe id="frame" class="w-full h-full"></iframe>
        </div>
    </div>

    <footer class="border-t bg-white">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="font-black text-lg"><span class="text-brand">PDF</span>Tools</div>
                <div class="text-xs text-gray-500">Simple PDF tools for everyday work.</div>
            </div>
            <div class="text-sm text-gray-500">© {{date('Y')}} PDFTools</div>
            <a href="{{route('home')}}#tools" class="text-sm font-bold text-gray-700">All Tools →</a>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let file = null,
        outputUrl = null;
    const $ = id => document.getElementById(id);
    const input = $("input"),
        chooseBtn = $("chooseBtn"),
        deleteBtn = $("deleteBtn"),
        box = $("box"),
        options = $("options"),
        compress = $("compress"),
        progressBox = $("progressBox"),
        bar = $("bar"),
        pct = $("pct"),
        ptext = $("ptext"),
        errorBox = $("errorBox"),
        paymentBox = $("paymentBox"),
        result = $("result"),
        download = $("download"),
        viewer = $("viewer"),
        frame = $("frame");

    chooseBtn.onclick = () => input.click();
    input.onchange = e => {
        if (e.target.files.length) setFile(e.target.files[0])
    };

    function setFile(f) {
        if (f.size === 0) {
            input.value = "";
            return showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !f.name.toLowerCase().endsWith(".pdf")) return showError(
            "Please select a valid PDF file.");
        file = f;
        $("name").textContent = f.name;
        $("size").textContent = formatBytes(f.size);
        box.classList.remove("hidden");
        options.classList.remove("hidden");
        paymentBox.classList.add("hidden");
        result.classList.add("hidden");
        errorBox.classList.add("hidden");
    }

    deleteBtn.onclick = () => {
        file = null;
        input.value = "";
        box.classList.add("hidden");
        options.classList.add("hidden");
        result.classList.add("hidden");
        paymentBox.classList.add("hidden");
        errorBox.classList.add("hidden");
        if (outputUrl) {
            URL.revokeObjectURL(outputUrl);
            outputUrl = null
        }
    };

    function formatBytes(b) {
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB"
    }

    function showError(m) {
        errorBox.innerHTML = "<b>Compression failed:</b><br>" + m;
        errorBox.classList.remove("hidden")
    }

    function progress(v, t) {
        v = Math.max(0, Math.min(100, v));
        bar.style.width = v + "%";
        pct.textContent = Math.round(v) + "%";
        ptext.textContent = t
    }

    async function reserveUpload() {
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
        } catch (e) {
            throw new Error("Server returned an invalid response.")
        }
        return {
            ok: r.ok,
            status: r.status,
            ...d
        };
    }

    function updateUsage(d) {
        if (typeof d.free_uploads_used !== "undefined") {
            const used = Number(d.free_uploads_used);
            $("usedCount").textContent = used + " / 4";
            const rem = Math.max(0, 4 - used);
            $("usageText").textContent = rem + " free file upload" + (rem === 1 ? "" : "s") +
                " remaining across all PDF tools.";
        }
    }

    async function loadPdf(bytes) {
        const task = pdfjsLib.getDocument({
            data: new Uint8Array(bytes),
            stopAtErrors: false,
            disableAutoFetch: false,
            disableStream: false,
            isEvalSupported: false
        });
        return await task.promise;
    }

    async function compressPDF() {
        if (!file) return showError("Please select a PDF file first.");
        errorBox.classList.add("hidden");
        paymentBox.classList.add("hidden");
        compress.disabled = true;
        compress.textContent = "Checking Free Upload...";
        try {
            const usage = await reserveUpload();
            updateUsage(usage);
            if (usage.requires_payment === true || usage.allowed === false) {
                paymentBox.classList.remove("hidden");
                compress.disabled = false;
                compress.textContent = "Compress PDF";
                return
            }
            progressBox.classList.remove("hidden");
            progress(5, "Reading PDF...");
            const bytes = await file.arrayBuffer();
            if (!bytes || bytes.byteLength < 20) throw new Error("The selected PDF file is empty or damaged.");
            progress(12, "Opening PDF structure...");
            let pdf;
            try {
                pdf = await loadPdf(bytes)
            } catch (e) {
                console.error("PDF.js error:", e);
                throw new Error(
                    "This PDF has an invalid or damaged internal structure. Please open it in Adobe Acrobat/Chrome and save it again as a new PDF, then upload that file."
                    )
            }
            const pages = pdf.numPages;
            if (!pages) throw new Error("No pages were found in this PDF.");
            progress(18, "Preparing " + pages + " page(s)...");
            const newPdf = await PDFLib.PDFDocument.create();

            for (let i = 1; i <= pages; i++) {
                const page = await pdf.getPage(i),
                    viewport = page.getViewport({
                        scale: .75
                    }),
                    canvas = document.createElement("canvas");
                canvas.width = Math.max(1, Math.ceil(viewport.width));
                canvas.height = Math.max(1, Math.ceil(viewport.height));
                const ctx = canvas.getContext("2d", {
                    alpha: false
                });
                ctx.fillStyle = "#fff";
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({
                    canvasContext: ctx,
                    viewport,
                    background: "white"
                }).promise;
                const jpg = canvas.toDataURL("image/jpeg", .55),
                    image = await newPdf.embedJpg(jpg),
                    newPage = newPdf.addPage([viewport.width, viewport.height]);
                newPage.drawImage(image, {
                    x: 0,
                    y: 0,
                    width: viewport.width,
                    height: viewport.height
                });
                progress(20 + (i / pages) * 65, "Compressing page " + i + " of " + pages + "...");
                canvas.width = 1;
                canvas.height = 1;
            }

            progress(90, "Creating compressed PDF...");
            const outputBytes = await newPdf.save({
                useObjectStreams: true,
                addDefaultPage: false
            });
            if (!outputBytes || outputBytes.length < 100) throw new Error(
                "The compressed PDF could not be created.");
            progress(97, "Finishing...");
            const blob = new Blob([outputBytes], {
                type: "application/pdf"
            });
            if (outputUrl) URL.revokeObjectURL(outputUrl);
            outputUrl = URL.createObjectURL(blob);
            const oldSize = file.size,
                newSize = blob.size,
                saved = oldSize - newSize,
                savedPct = oldSize > 0 ? Math.max(0, saved / oldSize * 100) : 0;
            $("stats").textContent = "Original: " + formatBytes(oldSize) + " • Compressed: " + formatBytes(
                newSize) + " • Reduced by " + savedPct.toFixed(1) + "%";
            download.href = outputUrl;
            download.download = file.name.replace(/\.pdf$/i, "") + "_compressed.pdf";
            result.classList.remove("hidden");
            progress(100, "Compression completed.");
        } catch (e) {
            console.error(e);
            showError(e?.message || "Unknown PDF compression error.")
        } finally {
            compress.disabled = false;
            compress.textContent = "Compress PDF"
        }
    }

    compress.onclick = compressPDF;
    $("viewBtn").onclick = () => {
        if (outputUrl) {
            frame.src = outputUrl;
            viewer.classList.remove("hidden")
        }
    };
    $("closeViewer").onclick = () => {
        viewer.classList.add("hidden");
        frame.src = ""
    };
    viewer.onclick = e => {
        if (e.target === viewer) {
            viewer.classList.add("hidden");
            frame.src = ""
        }
    };
    window.addEventListener("beforeunload", () => {
        if (outputUrl) URL.revokeObjectURL(outputUrl)
    });
    </script>
</body>

</html>