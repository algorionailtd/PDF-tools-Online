<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Image to PDF - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
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

    .upload {
        transition: .25s ease
    }

    .upload:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 45px #0f172a14
    }

    .drag {
        background: #fff5f5 !important;
        border-color: #e5322d !important
    }

    .loader {
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff55;
        border-top-color: white;
        border-radius: 50%;
        animation: spin .7s linear infinite
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

<body class="bg-white text-slate-900">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0"><span
                    class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span><span
                    class="text-2xl font-black">Tools</span></a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a><a href="{{route('home')}}#tools"
                    class="text-brand">All Tools</a><a href="{{route('home')}}#workflows"
                    class="hover:text-brand">Workflows</a><a href="{{route('home')}}#about"
                    class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 lg:py-20 text-center">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">🖼️
                PDF Tool 07</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Image to <span
                    class="text-brand">PDF</span></h1>
            <p class="max-w-2xl mx-auto mt-5 text-slate-500 text-base sm:text-lg leading-7">Convert JPG, PNG and WebP
                images into a professional PDF instantly.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
        <div id="error" class="hidden mb-6 rounded-xl border border-red-200 bg-red-50 text-red-600 px-4 py-3 text-sm font-semibold">
        </div>
        <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold">Free Uploads</p>
                    <p id="usageText" class="text-xs text-slate-500 mt-1">You have 4 free file uploads available across
                        all PDF tools.</p>
                </div>
                <div class="text-sm font-black text-brand"><span id="usedCount">0</span> / 4</div>
            </div>
        </div>

        <div id="drop" class="upload bg-white border-2 border-dashed border-slate-300 hover:bg-red-50 rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 text-center cursor-pointer hover:border-brand ">
            <div
                class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-red-50 flex items-center justify-center text-3xl sm:text-4xl">
                🖼️</div>
            <h2 class="mt-5 text-xl sm:text-2xl font-black">Upload your Images</h2>
            <p class="mt-2 text-sm sm:text-base text-slate-500">Drag & drop images here or click to browse</p>
            <input id="input" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
            <button type="button" onclick="event.stopPropagation();input.click()"
                class="mt-6 w-full sm:w-auto bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg shadow-lg shadow-red-100">Choose
                Images</button>
            <p class="mt-4 text-xs text-slate-400">JPG, PNG, WebP • No frontend MB upload limit</p>
        </div>

        <div id="box" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="font-black text-lg">Selected Images</h3>
                    <p id="fileInfo" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button type="button" onclick="resetAll()"
                    class="bg-red-50 hover:bg-red-100 text-brand px-4 py-2.5 rounded-lg font-semibold">Delete</button>
            </div>

            <div id="list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-6"></div>

            <button id="convert" type="button" onclick="convertPDF()"
                class="mt-6 w-full bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold shadow-lg shadow-red-100">Convert
                to PDF →</button>

            <div id="progress" class="hidden mt-5">
                <div class="flex justify-between gap-4 text-sm mb-2"><span id="ptext">Processing...</span><span
                        id="pct">0%</span></div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="bar" class="h-full bg-brand rounded-full w-0 transition-all duration-300"></div>
                </div>
            </div>

            <x-payment-upgrade-card />
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 bg-black/90 z-[60] p-2 sm:p-4">
        <div class="bg-white w-full h-full rounded-xl sm:rounded-2xl overflow-hidden flex flex-col">
            <div class="p-3 sm:p-4 border-b flex items-center justify-between gap-4"><b class="text-sm sm:text-base">PDF
                    Preview</b><button type="button" onclick="closeViewer()"
                    class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg text-sm font-semibold">Close</button>
            </div>
            <iframe id="frame" class="w-full flex-1 border-0"></iframe>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-100 py-7">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row gap-4 justify-between items-center text-center sm:text-left">
            <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
        </div>
    </footer>

    <script>
    const $ = id => document.getElementById(id);
    let files = [],
        url = null;

    function fmt(b) {
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        if (b < 1073741824) return (b / 1048576).toFixed(2) + " MB";
        return (b / 1073741824).toFixed(2) + " GB"
    }

    function showError(m) {
        $("error").textContent = m;
        $("error").classList.remove("hidden");
        $("error").scrollIntoView({
            behavior: "smooth",
            block: "center"
        })
    }

    function hideError() {
        $("error").textContent = "";
        $("error").classList.add("hidden")
    }

    async function reserveUpload() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "{{csrf_token()}}";
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
            const d = await r.json();
            if (!r.ok && !d.requires_payment) throw new Error(d.message || "HTTP " + r.status);

            if (d.allowed && d.premium) {
                $("usedCount").textContent = "4";
                $("usageText").textContent = "Premium access active. Unlimited uploads are allowed.";
                $("paymentBox").classList.add("hidden");
                return true
            }

            if (d.allowed) {
                const used = Number(d.free_uploads_used ?? (4 - Number(d.remaining ?? 0))),
                    remaining = Math.max(0, Number(d.remaining ?? (4 - used)));
                $("usedCount").textContent = Math.min(4, used);
                $("usageText").textContent = remaining + " free upload" + (remaining === 1 ? "" : "s") +
                    " remaining across all PDF tools.";
                $("paymentBox").classList.add("hidden");
                return true;
            }

            if (d.requires_payment) {
                $("usedCount").textContent = "4";
                $("usageText").textContent = "Your 4 free file uploads have been used.";
                $("paymentBox").classList.remove("hidden");
                $("paymentBox").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                return false
            }
            showError(d.message || "Upload is not allowed.");
            return false;
        } catch (e) {
            console.error("Usage reservation error:", e);
            showError("Unable to check upload access. Please try again.");
            return false
        }
    }

    function loadFiles(list) {
        hideError();
        const selected = Array.from(list || []);
        if (!selected.length) return;
        const emptyFiles = selected.filter(f => f.size === 0);
        if (emptyFiles.length) {
            $("input").value = "";
            return showError(emptyFiles.map(f => '"' + f.name + '"').join(', ') +
                ' is empty (0 bytes). Please choose non-empty images.');
        }
        if (selected.some(f => !["image/jpeg", "image/png", "image/webp"].includes(f.type))) return showError(
            "Only JPG, PNG and WebP images are allowed.");
        files = selected;
        $("fileInfo").textContent = files.length + " image" + (files.length === 1 ? "" : "s") + " selected • " + fmt(
            files.reduce((s, f) => s + f.size, 0));
        $("list").innerHTML = "";
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const card = document.createElement("div");
                card.className = "relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50";
                card.innerHTML =
                    `<img src="${e.target.result}" class="w-full h-32 object-cover" alt="Selected image"><div class="p-2 text-xs font-semibold truncate">${index+1}. ${escapeHtml(file.name)}</div>`;
                $("list").appendChild(card);
            };
            reader.readAsDataURL(file);
        });
        $("box").classList.remove("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        $("progress").classList.add("hidden");
    }

    function escapeHtml(v) {
        return String(v).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
    }

    $("input").onchange = e => loadFiles(e.target.files);
    $("drop").onclick = e => {
        if (!e.target.closest("button")) $("input").click()
    };
    ["dragenter", "dragover"].forEach(t => $("drop").addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        $("drop").classList.add("drag")
    }));
    ["dragleave", "drop"].forEach(t => $("drop").addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        $("drop").classList.remove("drag")
    }));
    $("drop").ondrop = e => loadFiles(e.dataTransfer.files);

    async function convertPDF() {
        hideError();
        if (!files.length) return showError("Please upload at least one image.");
        if (!await reserveUpload()) return;

        $("convert").disabled = true;
        $("convert").innerHTML =
            '<span class="inline-flex items-center justify-center gap-2"><span class="loader"></span>Creating PDF...</span>';
        $("progress").classList.remove("hidden");
        $("bar").style.width = "5%";
        $("pct").textContent = "5%";

        try {
            const pdf = await PDFLib.PDFDocument.create();
            for (let i = 0; i < files.length; i++) {
                $("ptext").textContent = `Processing image ${i+1} of ${files.length}...`;
                const bytes = await files[i].arrayBuffer();
                let image;
                if (files[i].type === "image/png") image = await pdf.embedPng(bytes);
                else image = await pdf.embedJpg(bytes);
                const dimensions = image.scale(1),
                    page = pdf.addPage([dimensions.width, dimensions.height]);
                page.drawImage(image, {
                    x: 0,
                    y: 0,
                    width: dimensions.width,
                    height: dimensions.height
                });
                const percent = Math.round(((i + 1) / files.length) * 90) + 5;
                $("bar").style.width = percent + "%";
                $("pct").textContent = percent + "%";
            }
            $("ptext").textContent = "Saving PDF...";
            const bytes = await pdf.save();
            if (!bytes || !bytes.length) throw new Error("Empty PDF generated.");
            if (url) URL.revokeObjectURL(url);
            url = URL.createObjectURL(new Blob([bytes], {
                type: "application/pdf"
            }));
            $("download").href = url;
            $("download").download = "images-to-pdf.pdf";
            $("stats").innerHTML =
                `${files.length} image${files.length===1?"":"s"} converted • ${fmt(bytes.length)}`;
            $("bar").style.width = "100%";
            $("pct").textContent = "100%";
            $("ptext").textContent = "Complete!";
            $("result").classList.remove("hidden");
            $("result").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        } catch (e) {
            console.error("Image to PDF error:", e);
            showError("Unable to create PDF. Your browser may not have enough memory for these images.");
        } finally {
            $("convert").disabled = false;
            $("convert").textContent = "Convert to PDF →"
        }
    }

    function viewPDF() {
        if (!url) return alert("Please create the PDF first.");
        $("frame").src = url;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden")
    }

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("frame").src = "";
        document.body.classList.remove("overflow-hidden")
    }

    function resetAll() {
        closeViewer();
        if (url) {
            URL.revokeObjectURL(url);
            url = null
        }
        files = [];
        $("input").value = "";
        $("box").classList.add("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        $("progress").classList.add("hidden");
        $("list").innerHTML = "";
        $("bar").style.width = "0%";
        $("pct").textContent = "0%";
        $("ptext").textContent = "Processing...";
        $("convert").disabled = false;
        $("convert").textContent = "Convert to PDF →";
        hideError();
    }
    window.addEventListener("beforeunload", () => {
        if (url) URL.revokeObjectURL(url)
    });
    </script>
</body>

</html>