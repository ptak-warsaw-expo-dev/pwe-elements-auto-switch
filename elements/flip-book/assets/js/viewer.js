(function () {
  function qs(root, sel) { return root.querySelector(sel); }

  class LRUCache {
    constructor(limit) { this.limit = limit; this.map = new Map(); }
    get(k) {
      if (!this.map.has(k)) return null;
      const v = this.map.get(k);
      this.map.delete(k);
      this.map.set(k, v);
      return v;
    }
    set(k, v) {
      if (this.map.has(k)) this.map.delete(k);
      this.map.set(k, v);
      while (this.map.size > this.limit) {
        const firstKey = this.map.keys().next().value;
        this.map.delete(firstKey);
      }
    }
    clear() { this.map.clear(); }
  }

  async function renderPdfPageToCanvas(pdfDoc, pageNum, scale) {
    const page = await pdfDoc.getPage(pageNum);
    const dpr = window.devicePixelRatio || 1;
    const viewport = page.getViewport({ scale: scale });
    
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d", { alpha: false });

    canvas.width = Math.floor(viewport.width * dpr);
    canvas.height = Math.floor(viewport.height * dpr);

    canvas.style.width = "100%";
    canvas.style.height = "100%";
    canvas.style.objectFit = "contain";

    const transform = [dpr, 0, 0, dpr, 0, 0];
    const renderContext = {
      canvasContext: ctx,
      transform: transform,
      viewport: viewport
    };

    await page.render(renderContext).promise;
    return canvas;
  }

  async function initOne(cfg) {
    const root = document.getElementById(cfg.id);
    if (!root) return;

    const stage = qs(root, ".pwe-flipbook-stage");
    const info = qs(root, '[data-role="pageinfo"]');
    
    console.log('[PWE Flipbook] Initializing flipbook with config:', cfg);
    
    // --- ERROR HANDLING WRAPPER ---
    try {
        // Dynamic import of PDF.js
        console.log('[PWE Flipbook] Loading PDF.js from:', cfg.pdfModuleSrc);
        const pdfjsLib = await import(cfg.pdfModuleSrc);
        pdfjsLib.GlobalWorkerOptions.workerSrc = cfg.workerSrc;
        console.log('[PWE Flipbook] PDF.js loaded successfully');

        const btnPrev = qs(root, '[data-nav="prev"]');
        const btnNext = qs(root, '[data-nav="next"]');

        console.log('[PWE Flipbook] Loading PDF from URL:', cfg.pdf);
        const loadingTask = pdfjsLib.getDocument({
          url: cfg.pdf,
          disableStream: false,
          disableAutoFetch: false
        });

        const pdfDoc = await loadingTask.promise;
        const total = pdfDoc.numPages;
        console.log('[PWE Flipbook] PDF loaded successfully, total pages:', total);

        const page1 = await pdfDoc.getPage(1);
        const viewport1 = page1.getViewport({ scale: 1 });
        const pdfAspectRatio = viewport1.width / viewport1.height;
        
        const stageWidth = stage.clientWidth;
        const isMobile = window.matchMedia("(max-width: 768px)").matches;
        
        let baseHeight = isMobile 
            ? stageWidth / pdfAspectRatio 
            : (stageWidth * 0.5) / pdfAspectRatio;
            
        baseHeight = Math.min(baseHeight, window.innerHeight * 0.8);
        let renderScale = isMobile ? 1.5 : 2.0; 

        const cache = new LRUCache(12);

        // --- INIT STPAGEFLIP ---
        const pageFlip = new St.PageFlip(stage, {
          width: viewport1.width,
          height: viewport1.height,
          size: "stretch",
          minWidth: 300,
          maxWidth: 2000,
          minHeight: 400,
          maxHeight: 2500,
          showCover: true,
          useMouseEvents: false, 
          drawShadow: true,
          mobileScrollSupport: true
        });

        if (!isMobile) {
            stage.addEventListener('click', (e) => {
                if (e.target.closest('.pwe-nav-arrow') || e.target.closest('button')) return;
                const rect = stage.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const halfWidth = rect.width / 2;
                if (clickX < halfWidth) pageFlip.flipPrev();
                else pageFlip.flipNext();
            });
        }

        const pageEls = [];
        for (let i = 1; i <= total; i++) {
          const el = document.createElement("div");
          el.className = "pwe-page";
          el.style.position = "relative"; 
          el.style.overflow = "hidden";
          el.style.backgroundColor = "#fff"; 
          pageEls.push(el);
        }

        pageFlip.loadFromHTML(pageEls);

        async function ensureRendered(pageNum) {
          if (pageNum < 1 || pageNum > total) return;
          const targetEl = pageEls[pageNum - 1];
          const existingCanvas = targetEl.querySelector('.pwe-canvas-base');
          if (existingCanvas) return;

          const key = `page-${pageNum}-scale-${renderScale}`;
          let canvas = cache.get(key);

          if (!canvas) {
            canvas = await renderPdfPageToCanvas(pdfDoc, pageNum, renderScale);
            canvas.classList.add('pwe-canvas-base');
            cache.set(key, canvas);
          }

          targetEl.innerHTML = ""; 
          targetEl.appendChild(canvas);
          attachHoverZoom(targetEl, pageNum);
        }

        async function renderAround(currentPage) {
            const queue = [currentPage];
            if (currentPage + 1 <= total) queue.push(currentPage + 1);
            if (currentPage - 1 >= 1) queue.push(currentPage - 1);
            if (currentPage + 2 <= total) queue.push(currentPage + 2);
            if (currentPage + 3 <= total) queue.push(currentPage + 3);

            console.log('[PWE Flipbook] renderAround starting for pages:', queue);
            for (const p of queue) {
                try {
                    console.log('[PWE Flipbook] Rendering page:', p);
                    await ensureRendered(p);
                    console.log('[PWE Flipbook] Page rendered successfully:', p);
                } catch (err) {
                    console.error('[PWE Flipbook] Error rendering page', p, ':', err);
                }
            }
            console.log('[PWE Flipbook] renderAround completed');
        }

        function updateInfo() {
            const currentIndices = pageFlip.getOrientation() === 'portrait' 
                ? [pageFlip.getCurrentPageIndex()] 
                : [pageFlip.getCurrentPageIndex(), pageFlip.getCurrentPageIndex() + 1];
                
            let text = "";
            const p1 = currentIndices[0] + 1;
            
            if (currentIndices.length > 1) {
                const p2 = currentIndices[1] + 1;
                text = (p2 <= total) ? `${p1}-${p2} / ${total}` : `${p1} / ${total}`;
            } else {
                 text = `${p1} / ${total}`;
            }

            if(info) info.textContent = text;
            updateArrows(p1, total);
            return p1;
        }

        function updateArrows(currentLeftPage, totalPages) {
            if (!btnPrev || !btnNext) return;
            btnPrev.classList.remove('disabled');
            btnPrev.disabled = false;
            btnNext.classList.remove('disabled');
            btnNext.disabled = false;

            if (currentLeftPage <= 1) {
                btnPrev.classList.add('disabled');
                btnPrev.disabled = true;
            }

            const isPortrait = pageFlip.getOrientation() === 'portrait';
            const currentRightPage = isPortrait ? currentLeftPage : currentLeftPage + 1;

            if (currentRightPage >= totalPages) {
                btnNext.classList.add('disabled');
                btnNext.disabled = true;
            }
        }

        function attachHoverZoom(pageEl, pageNum) {
            if (pageEl.dataset.zoomAttached) return;
            pageEl.dataset.zoomAttached = "true";
            if (window.matchMedia("(hover: none)").matches) return; 

            pageEl.addEventListener('mousemove', async (e) => {
                const stage = pageEl.closest('.pwe-flipbook-stage');
                if (!stage) return;
                const rect = pageEl.getBoundingClientRect();
                const yRatio = (e.clientY - rect.top) / rect.height;
                let zoomImg = stage.querySelector(`.pwe-zoom-layer[data-page="${pageNum}"]`);
                
                if (!zoomImg) {
                    if (pageEl.dataset.zoomLoading) return;
                    pageEl.dataset.zoomLoading = "true";
                    const zoomScale = renderScale * 2.5;
                    const zCanvas = await renderPdfPageToCanvas(pdfDoc, pageNum, zoomScale);
                    zCanvas.className = 'pwe-zoom-layer';
                    zCanvas.dataset.page = pageNum; 
                    zCanvas.style.position = 'absolute';
                    zCanvas.style.top = '0';
                    zCanvas.style.left = '0';
                    zCanvas.style.width = '100%';
                    zCanvas.style.height = 'auto'; 
                    zCanvas.style.zIndex = '1000'; 
                    zCanvas.style.pointerEvents = 'none'; 
                    zCanvas.style.opacity = '0';
                    zCanvas.style.transition = 'opacity 0.2s, transform 0.1s linear';
                    zCanvas.style.backgroundColor = '#fff'; 
                    stage.appendChild(zCanvas);
                    zoomImg = zCanvas;
                    pageEl.dataset.zoomLoading = "";
                }

                if (zoomImg) {
                    const others = stage.querySelectorAll(`.pwe-zoom-layer:not([data-page="${pageNum}"])`);
                    others.forEach(el => el.remove());
                    zoomImg.style.opacity = '1';
                    const scrollRange = zoomImg.offsetHeight - stage.offsetHeight;
                    if (scrollRange > 0) {
                        const moveY = -yRatio * scrollRange;
                        zoomImg.style.transform = `translate(0, ${moveY}px)`;
                    } else {
                        const centerOffset = (stage.offsetHeight - zoomImg.offsetHeight) / 2;
                        zoomImg.style.transform = `translate(0, ${centerOffset}px)`;
                    }
                }
            });

            pageEl.addEventListener('mouseleave', () => {
                const stage = pageEl.closest('.pwe-flipbook-stage');
                if (!stage) return;
                const zoomImg = stage.querySelector(`.pwe-zoom-layer[data-page="${pageNum}"]`);
                if (zoomImg) {
                    zoomImg.style.opacity = '0';
                    setTimeout(() => {
                        if (zoomImg && zoomImg.style.opacity === '0') zoomImg.remove();
                    }, 300);
                }
            });
        }

        pageFlip.on("flip", (e) => {
            updateInfo();
            renderAround(e.data + 1);
        });

        if (btnPrev) btnPrev.addEventListener('click', () => pageFlip.flipPrev());
        if (btnNext) btnNext.addEventListener('click', () => pageFlip.flipNext());
        
        await renderAround(cfg.start);
        if (cfg.start > 1) {
            pageFlip.flip(cfg.start - 1, 'bottom'); 
        }
        updateInfo();

    } catch (error) {
        // TEN BLOK POKAŻE CI BŁĄD NA EKRANIE ZAMIAST "LOADING"
        console.error("Flipbook Init Error:", error);
        if(info) {
            info.innerHTML = `<span style="color:red; font-weight:bold;">Error: ${error.message}</span>.`;
        }
    }
  }

  async function boot() {
    const list = window.PWE_FLIPBOOK || [];
    for (const cfg of list) {
      await initOne(cfg);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();