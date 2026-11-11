<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Signature - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
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
    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif
    }
        html {
        scrollbar-width: none
    }


    .grid-bg {
        background-image: linear-gradient(rgba(229, 50, 45, .055) 1px, transparent 1px), linear-gradient(90deg, rgba(229, 50, 45, .055) 1px, transparent 1px);
        background-size: 28px 28px
    }
    </style>
</head>

<body class="bg-white text-slate-900">

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
            <a href="{{route('home')}}" class="flex items-center gap-2">
                <span
                    class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-black">PDF</span>
                <div>
                    <div class="font-black text-lg leading-none">Tools</div>
                    <div class="text-[10px] text-slate-400 font-semibold">Every PDF tool</div>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="{{route('home')}}" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="hover:text-brand">All Tools</a>
                <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:inline-flex bg-brand hover:bg-brandDark text-white px-5 py-2.5 rounded-xl text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="grid-bg py-14 sm:py-20 border-b border-red-50">
        <div class="max-w-4xl mx-auto px-5 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-brand text-sm font-bold">✍️
                PDF Tool 11</div>
            <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight">Add Signature to PDF</h1>
            <p class="mt-4 text-slate-500 max-w-2xl mx-auto text-base sm:text-lg">Upload your PDF, add your signature
                and download the signed PDF instantly.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-5 py-10">

        <div
            class="mb-6 rounded-2xl bg-blue-50 border border-blue-100 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="font-black text-blue-900">Free Uploads</div>
                <div id="usageMessage" class="text-sm text-blue-700 mt-1">You have 4 free file uploads available.</div>
            </div>
            <div class="text-2xl font-black text-blue-900"><span id="usageCount">0</span> / 4</div>
        </div>

        <div id="uploadCard" class="border-2 border-dashed border-slate-300 hover:bg-red-50 hover:border-red-300 rounded-3xl p-8 sm:p-12 text-center bg-white">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-brand flex items-center justify-center text-3xl">📄
            </div>
            <h2 class="mt-5 text-2xl font-black">Upload your PDF</h2>
            <p class="mt-2 text-slate-500">Drag & drop your PDF here or choose a file.</p>
            <p class="mt-1 text-xs text-slate-400">PDF • No frontend MB upload limit</p>
            <input type="file" id="pdfInput" accept="application/pdf" class="hidden">
            <button type="button" onclick="document.getElementById('pdfInput').click()"
                class="mt-6 bg-brand hover:bg-brandDark text-white px-7 py-3 rounded-xl font-bold">Choose PDF</button>
        </div>

        <div id="fileCard" class="hidden mt-6 rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-red-50 flex items-center justify-center text-2xl">📄
                    </div>
                    <div class="min-w-0">
                        <div id="fileName" class="font-bold truncate"></div>
                        <div id="fileSize" class="text-sm text-slate-400"></div>
                    </div>
                </div>
                <button type="button" onclick="removePDF()" class="text-sm font-bold text-red-600">Delete</button>
            </div>
        </div>

        <div id="optionsCard" class="hidden mt-6 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center text-xl">✍️</div>
                <div>
                    <h2 class="font-black text-xl">Signature Options</h2>
                    <p class="text-sm text-slate-400">Upload your signature and choose where to place it.</p>
                </div>
            </div>

            <label class="block text-sm font-bold text-slate-700 mb-2">Signature Image</label>
            <input type="file" id="signatureInput" accept="image/png,image/jpeg"
                class="block w-full text-sm border border-slate-200 rounded-xl p-3 cursor-pointer">

            <div id="signaturePreviewBox" class="hidden mt-5">
                <div class="text-sm font-bold text-slate-700 mb-2">Signature Preview</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                    <img id="signaturePreview" class="max-h-28 max-w-full mx-auto object-contain">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Page Number</label>
                <input type="number" id="pageNumber" min="1" value="1"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-brand">
            </div>

            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-3">Signature Position</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer"><input type="radio" name="position" value="top" class="peer hidden">
                        <div
                            class="rounded-xl border border-slate-200 p-4 text-center peer-checked:border-brand peer-checked:bg-red-50">
                            <div>⬆️</div>
                            <div class="text-sm font-bold mt-1">Top</div>
                        </div>
                    </label>
                    <label class="cursor-pointer"><input type="radio" name="position" value="center" class="peer hidden"
                            checked>
                        <div
                            class="rounded-xl border border-slate-200 p-4 text-center peer-checked:border-brand peer-checked:bg-red-50">
                            <div>🎯</div>
                            <div class="text-sm font-bold mt-1">Center</div>
                        </div>
                    </label>
                    <label class="cursor-pointer"><input type="radio" name="position" value="bottom"
                            class="peer hidden">
                        <div
                            class="rounded-xl border border-slate-200 p-4 text-center peer-checked:border-brand peer-checked:bg-red-50">
                            <div>⬇️</div>
                            <div class="text-sm font-bold mt-1">Bottom</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex justify-between mb-2"><label class="text-sm font-bold text-slate-700">Signature
                        Size</label><span id="sizeValue" class="text-sm font-bold text-brand">180 px</span></div>
                <input id="signatureSize" type="range" min="60" max="400" value="180" class="w-full accent-red-600">
            </div>

            <button id="generateBtn" type="button" onclick="generateSignedPDF()"
                class="mt-7 w-full bg-brand hover:bg-brandDark text-white py-4 rounded-xl font-black">✍️ Generate Signed
                PDF</button>
            <div id="progressBox" class="hidden mt-5">
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="progressBar" class="h-full bg-brand transition-all" style="width:0%"></div>
                </div>
                <div id="progressText" class="text-center text-sm text-slate-500 mt-2">Preparing...</div>
            </div>
            <div id="errorBox"
                class="hidden mt-5 rounded-xl bg-red-50 border border-red-100 text-red-700 p-4 text-sm font-semibold">
            </div>
        </div>

        <x-payment-upgrade-card id="paymentCard" />

        <div id="resultCard" class="hidden mt-6 rounded-3xl border border-green-200 bg-white overflow-hidden">
            <div class="bg-green-50 border-b border-green-100 p-5">
                <div class="text-green-700 font-black text-lg">✓ Signed PDF Ready</div>
                <div id="resultInfo" class="text-sm text-green-600 mt-1"></div>
            </div>
            <div class="p-6 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="viewPDF()"
                    class="flex-1 bg-slate-900 hover:bg-black text-white py-3.5 rounded-xl font-black">👁 View
                    PDF</button>
                <a id="downloadBtn"
                    class="flex-1 bg-brand hover:bg-brandDark text-white py-3.5 rounded-xl font-black text-center cursor-pointer">⬇
                    Download PDF</a>
            </div>
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 z-50 bg-black/90 p-3 sm:p-6">
        <div class="h-full max-w-6xl mx-auto bg-white rounded-2xl overflow-hidden relative">
            <button type="button" onclick="closeViewer()"
                class="absolute right-3 top-3 z-10 w-10 h-10 rounded-full bg-black text-white font-black">×</button>
            <iframe id="pdfFrame" class="w-full h-full" title="PDF Preview"></iframe>
        </div>
    </div>

    <footer class="border-t border-slate-100 mt-10">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="font-black text-lg">PDF<span class="text-brand">Tools</span></div>
                <div class="text-sm text-slate-400 mt-1">Simple, fast and reliable online PDF tools.</div>
            </div>
            <a href="{{route('home')}}#tools" class="text-sm font-bold text-slate-600">All Tools</a>
        </div>
        
    </footer>

    <script>
    let pdfFile = null,
        signatureFile = null,
        signedUrl = null;
    const FREE_LIMIT = 4,
        $ = id => document.getElementById(id);

    function formatSize(b) {
        return b < 1024 ? b + " B" : b < 1048576 ? (b / 1024).toFixed(1) + " KB" : (b / 1048576).toFixed(1) + " MB"
    }

    function showPayment() {
        $("paymentCard").classList.remove("hidden");
        $("paymentCard").scrollIntoView({
            behavior: "smooth",
            block: "center"
        })
    }
    async function reserveUpload() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "{{csrf_token()}}";
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
        if (d.requires_payment) {
            showPayment();
            throw new Error("FREE_UPLOAD_FINISHED")
        }
        if (!r.ok || !d.allowed) throw new Error(d.message || "Upload could not be reserved.");
        if (typeof d.free_uploads_used !== "undefined") {
            $("usageCount").textContent = d.free_uploads_used;
            let rem = Math.max(0, FREE_LIMIT - Number(d.free_uploads_used));
            $("usageMessage").textContent = rem > 0 ? "You have " + rem + " free file uploads available." :
                "Free uploads finished. Upgrade to continue."
        }
        return d
    }
    $("pdfInput").addEventListener("change", function() {
        let f = this.files[0];
        if (!f) return;
        if (f.size === 0) {
            showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
            this.value = "";
            return
        }
        if (f.type !== "application/pdf") {
            showError("Please select a valid PDF file.");
            this.value = "";
            return
        }
        pdfFile = f;
        $("fileName").textContent = f.name;
        $("fileSize").textContent = formatSize(f.size);
        $("fileCard").classList.remove("hidden");
        $("optionsCard").classList.remove("hidden");
        hideError()
    });
    $("signatureInput").addEventListener("change", function() {
        let f = this.files[0];
        if (!f) return;
        if (f.size === 0) {
            showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty signature image.');
            this.value = "";
            return
        }
        if (!["image/png", "image/jpeg"].includes(f.type)) {
            showError("Please select a PNG or JPG signature image.");
            this.value = "";
            return
        }
        signatureFile = f;
        let r = new FileReader();
        r.onload = e => {
            $("signaturePreview").src = e.target.result;
            $("signaturePreviewBox").classList.remove("hidden")
        };
        r.readAsDataURL(f);
        hideError()
    });
    $("signatureSize").addEventListener("input", function() {
        $("sizeValue").textContent = this.value + " px"
    });

    function removePDF() {
        pdfFile = null;
        $("pdfInput").value = "";
        $("fileCard").classList.add("hidden");
        $("optionsCard").classList.add("hidden");
        $("resultCard").classList.add("hidden");
        if (signedUrl) {
            URL.revokeObjectURL(signedUrl);
            signedUrl = null
        }
    }
    async function generateSignedPDF() {
        if (!pdfFile) return showError("Please upload a PDF first.");
        if (!signatureFile) return showError("Please upload your signature image.");
        hideError();
        $("generateBtn").disabled = true;
        $("generateBtn").classList.add("opacity-60", "cursor-not-allowed");
        $("progressBox").classList.remove("hidden");
        try {
            updateProgress(10, "Validating PDF...");
            const pdfBytes = await pdfFile.arrayBuffer();
            const pdfHeader = new TextDecoder().decode(pdfBytes.slice(0, 1024));
            if (!pdfHeader.includes("%PDF-")) {
                throw new Error('"' + pdfFile.name + '" does not contain a valid PDF header. Re-download or export the original PDF.');
            }

            let pdfDoc;
            try {
                pdfDoc = await PDFLib.PDFDocument.load(pdfBytes, {
                    ignoreEncryption: true,
                    updateMetadata: false
                });
            } catch (error) {
                if (/No PDF header found|Failed to parse PDF document/i.test(error.message || "")) {
                    throw new Error('"' + pdfFile.name + '" has an invalid PDF structure. Re-download or export the original PDF.');
                }
                throw error;
            }

            updateProgress(25, "Checking free upload...");
            await reserveUpload();
            updateProgress(45, "Reading signature...");
            let bytes = await signatureFile.arrayBuffer(),
                image = signatureFile.type === "image/png" ? await pdfDoc.embedPng(bytes) : await pdfDoc.embedJpg(
                    bytes);
            updateProgress(60, "Adding signature...");
            let pages = pdfDoc.getPages(),
                pn = parseInt($("pageNumber").value, 10) || 1;
            pn = Math.max(1, Math.min(pn, pages.length));
            let page = pages[pn - 1],
                pos = document.querySelector('input[name="position"]:checked')?.value || "center",
                w = parseInt($("signatureSize").value, 10) || 180,
                scale = w / image.width,
                width = w,
                height = image.height * scale,
                pw = page.getWidth(),
                ph = page.getHeight(),
                x = (pw - width) / 2,
                y = (ph - height) / 2;
            if (pos === "top") y = ph - height - 40;
            if (pos === "bottom") y = 40;
            x = Math.max(10, x);
            y = Math.max(10, y);
            page.drawImage(image, {
                x,
                y,
                width,
                height
            });
            updateProgress(80, "Creating signed PDF...");
            let out = await pdfDoc.save({
                    useObjectStreams: false,
                    addDefaultPage: false
                }),
                blob = new Blob([out], {
                    type: "application/pdf"
                });
            if (signedUrl) URL.revokeObjectURL(signedUrl);
            signedUrl = URL.createObjectURL(blob);
            $("downloadBtn").href = signedUrl;
            $("downloadBtn").download = pdfFile.name.replace(/\.pdf$/i, "") + "-signed.pdf";
            $("resultInfo").textContent = "Signature added on page " + pn + " • " + formatSize(blob.size);
            $("resultCard").classList.remove("hidden");
            updateProgress(100, "Signed PDF ready.")
        } catch (e) {
            if (e.message === "FREE_UPLOAD_FINISHED") {
                updateProgress(0, "Payment required.");
                return
            }
            console.error(e);
            showError(e.message || "Could not create the signed PDF.");
            updateProgress(0, "Something went wrong.")
        } finally {
            $("generateBtn").disabled = false;
            $("generateBtn").classList.remove("opacity-60", "cursor-not-allowed")
        }
    }

    function viewPDF() {
        if (!signedUrl) return showError("Please generate the signed PDF first.");
        $("pdfFrame").src = signedUrl;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden")
    }

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("pdfFrame").src = "";
        document.body.classList.remove("overflow-hidden")
    }

    function updateProgress(p, t) {
        $("progressBar").style.width = p + "%";
        $("progressText").textContent = t
    }

    function showError(m) {
        $("errorBox").textContent = m;
        $("errorBox").classList.remove("hidden")
    }

    function hideError() {
        $("errorBox").classList.add("hidden");
        $("errorBox").textContent = ""
    }
    document.addEventListener("keydown", e => {
        if (e.key === "Escape" && !$("viewer").classList.contains("hidden")) closeViewer()
    });
    </script>
</body>

</html>