(function(){
    // Lightweight accessible modal for frontend
    function escHtml(s){ if(s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function createModal(){
        if(document.getElementById('aow-frontend-modal-root')) return;
        var root = document.createElement('div'); root.id = 'aow-frontend-modal-root'; root.className = 'aow-frontend-modal-root';
        root.innerHTML = '\n          <div class="aow-backdrop" data-aow-backdrop></div>\n          <div class="aow-modal" role="dialog" aria-modal="true" aria-hidden="true" data-aow-modal>\n            <div class="aow-modal-card" role="document">\n              <header class="aow-modal-header">\n                <strong id="aow-frontend-modal-title">Message</strong>\n                <button class="aow-modal-close" aria-label="Close">✕</button>\n              </header>\n              <div class="aow-modal-body" id="aow-frontend-modal-body"></div>\n              <div class="aow-modal-actions" id="aow-frontend-modal-actions"></div>\n            </div>\n          </div>\n        ';
        document.body.appendChild(root);
        // event bindings
        var closeBtn = root.querySelector('.aow-modal-close'); if(closeBtn) closeBtn.addEventListener('click', hideModal);
        var bd = root.querySelector('[data-aow-backdrop]'); if(bd) bd.addEventListener('click', hideModal);
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hideModal(); });
    }

    var focusedBefore = null;
    function trapFocus(modalRoot){
        var focusable = modalRoot.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])');
        if(!focusable.length) return;
        var first = focusable[0], last = focusable[focusable.length-1];
        function handleTab(e){ if(e.key !== 'Tab') return; if(e.shiftKey){ if(document.activeElement === first){ e.preventDefault(); last.focus(); } } else { if(document.activeElement === last){ e.preventDefault(); first.focus(); } } }
        modalRoot.__aow_tab_handler = handleTab;
        modalRoot.addEventListener('keydown', handleTab);
    }
    function releaseFocus(modalRoot){ if(!modalRoot.__aow_tab_handler) return; modalRoot.removeEventListener('keydown', modalRoot.__aow_tab_handler); modalRoot.__aow_tab_handler = null; }

    function showModal(title, htmlBody, actionsHtml){ createModal(); var root=document.getElementById('aow-frontend-modal-root'); var modal=root.querySelector('[data-aow-modal]'); if(!modal) return; modal.setAttribute('aria-hidden','false'); var titleEl=document.getElementById('aow-frontend-modal-title'); if(titleEl) titleEl.textContent = title||'Message'; var bodyEl=document.getElementById('aow-frontend-modal-body'); if(bodyEl) bodyEl.innerHTML = htmlBody||''; var actionsEl=document.getElementById('aow-frontend-modal-actions'); if(actionsEl) actionsEl.innerHTML = actionsHtml||'<button id="aow-frontend-modal-ok" class="aow-btn aow-btn-primary">OK</button>'; focusedBefore = document.activeElement; // show
        root.classList.add('visible');
        // focus first focusable element
        setTimeout(function(){ var focusable = modal.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'); if(focusable.length) focusable[0].focus(); trapFocus(modal); }, 20);
    }

    function hideModal(){ var root=document.getElementById('aow-frontend-modal-root'); if(!root) return; var modal=root.querySelector('[data-aow-modal]'); if(modal) modal.setAttribute('aria-hidden','true'); releaseFocus(modal); root.classList.remove('visible'); if(focusedBefore) try{ focusedBefore.focus(); }catch(e){} }

    function aow_confirm(message, cbYes, cbNo){ showModal('Confirm', '<div class="aow-modal-text">'+escHtml(message)+'</div>', '<button id="aow-frontend-modal-yes" class="aow-btn aow-btn-primary">Yes</button><button id="aow-frontend-modal-no" class="aow-btn">No</button>'); setTimeout(function(){ var y=document.getElementById('aow-frontend-modal-yes'); var n=document.getElementById('aow-frontend-modal-no'); if(y) y.onclick = function(){ hideModal(); if(typeof cbYes === 'function') cbYes(); }; if(n) n.onclick = function(){ hideModal(); if(typeof cbNo === 'function') cbNo(); }; }, 10); }

    function aow_prompt(message, defaultValue, cb){ showModal('Input Required', '<label class="aow-modal-label">'+escHtml(message)+'</label><input id="aow-frontend-modal-input" class="aow-modal-input" value="'+escHtml(defaultValue||'')+'" />', '<button id="aow-frontend-modal-submit" class="aow-btn aow-btn-primary">OK</button><button id="aow-frontend-modal-cancel" class="aow-btn">Cancel</button>'); setTimeout(function(){ var s=document.getElementById('aow-frontend-modal-submit'); var c=document.getElementById('aow-frontend-modal-cancel'); if(s) s.onclick = function(){ var v=document.getElementById('aow-frontend-modal-input').value; hideModal(); if(typeof cb === 'function') cb(v); }; if(c) c.onclick = function(){ hideModal(); if(typeof cb === 'function') cb(null); }; }, 10); }

    // Expose globally
    window.aow_showModal = showModal;
    window.aow_hideModal = hideModal;
    window.aow_confirm = aow_confirm;
    window.aow_prompt = aow_prompt;
})();
