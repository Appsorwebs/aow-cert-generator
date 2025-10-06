(function(){
    // Helper: HTML escape
    function escHtml(s){ if(s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    // Modal admin implementation (simple)
    function createAdminModalRoot(){
        if(document.getElementById('aow-admin-modal-root')) return;
        var root = document.createElement('div'); root.id = 'aow-admin-modal-root';
        root.innerHTML = '\n            <div id="aow-admin-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9998"></div>\n            <div id="aow-admin-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:20px;">\n                <div id="aow-admin-modal-card" style="max-width:900px;width:100%;background:#fff;color:#111;border-radius:8px;overflow:hidden;box-shadow:0 10px 40px rgba(2,6,23,0.6)">\n                    <div id="aow-admin-modal-header" style="padding:12px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;background:#f7fafc"><strong id="aow-admin-modal-title">Message</strong><button id="aow-admin-modal-close" style="background:none;border:0;font-size:18px;cursor:pointer">✕</button></div>\n                    <div id="aow-admin-modal-body" style="padding:16px;font-size:14px;max-height:60vh;overflow:auto"></div>\n                    <div id="aow-admin-modal-actions" style="padding:12px;border-top:1px solid #eee;text-align:right;background:#f7fafc"></div>\n                </div>\n            </div>\n        ';
        document.body.appendChild(root);
        document.getElementById('aow-admin-modal-close').onclick = aow_admin_hideModal;
    }

    function aow_admin_showModal(title, htmlBody, actionsHtml){ createAdminModalRoot(); var bd=document.getElementById('aow-admin-modal-backdrop'); var m=document.getElementById('aow-admin-modal'); document.getElementById('aow-admin-modal-title').textContent = title||'Message'; document.getElementById('aow-admin-modal-body').innerHTML = htmlBody||''; document.getElementById('aow-admin-modal-actions').innerHTML = actionsHtml||'<button id="aow-admin-modal-ok" class="button button-primary">OK</button>'; bd.style.display='block'; m.style.display='flex'; var ok=document.getElementById('aow-admin-modal-ok'); if(ok) ok.focus(); }
    function aow_admin_hideModal(){ var bd=document.getElementById('aow-admin-modal-backdrop'); var m=document.getElementById('aow-admin-modal'); if(bd) bd.style.display='none'; if(m) m.style.display='none'; }
    function aow_admin_confirm(message, cbYes, cbNo){ aow_admin_showModal('Confirm', '<div style="margin-bottom:8px">'+escHtml(message)+'</div>', '<button id="aow-admin-modal-yes" class="button button-primary">Yes</button><button id="aow-admin-modal-no" class="button" style="margin-left:8px">No</button>'); document.getElementById('aow-admin-modal-yes').onclick=function(){ aow_admin_hideModal(); if(typeof cbYes==='function') cbYes(); }; document.getElementById('aow-admin-modal-no').onclick=function(){ aow_admin_hideModal(); if(typeof cbNo==='function') cbNo(); }; }
    function aow_admin_prompt(message, defaultValue, cb){ aow_admin_showModal('Input Required', '<label style="display:block;margin-bottom:8px">'+escHtml(message)+'</label><input id="aow-admin-modal-input" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px" value="'+escHtml(defaultValue||'')+'"/>', '<button id="aow-admin-modal-submit" class="button button-primary">OK</button><button id="aow-admin-modal-cancel" class="button" style="margin-left:8px">Cancel</button>'); document.getElementById('aow-admin-modal-submit').onclick=function(){ var v=document.getElementById('aow-admin-modal-input').value; aow_admin_hideModal(); if(typeof cb==='function') cb(v); }; document.getElementById('aow-admin-modal-cancel').onclick=function(){ aow_admin_hideModal(); if(typeof cb==='function') cb(null); }; }

    // Expose helpers globally for other inline scripts to use
    window.aow_showModal = function(t,b,a){ aow_admin_showModal(t,b,a); };
    window.aow_hideModal = function(){ aow_admin_hideModal(); };
    window.aow_confirm = function(m,y,n){ aow_admin_confirm(m,y,n); };
    window.aow_prompt = function(m,d,cb){ aow_admin_prompt(m,d,cb); };
    window.escHtml = escHtml;

    // Utility: POST JSON wrapper using WP nonce localized later
    function fetchJson(url, body){
        var headers = {'Content-Type':'application/json'};
        if(window.AOW_REST && window.AOW_REST.nonce) headers['X-WP-Nonce'] = window.AOW_REST.nonce;
        return fetch(url,{method:'POST',headers:headers,body:JSON.stringify(body)}).then(function(r){ return r.json(); });
    }

    // Attach handlers for retry/purge/details buttons
    function initJobButtons(){
        document.querySelectorAll('.aow-retry-job').forEach(function(b){ b.addEventListener('click',function(){ var id=this.getAttribute('data-jobid'); if(!id) return; var btn=this; btn.disabled=true; btn.textContent='Retrying...'; fetchJson(window.AOW_REST.root + 'retry-job', {job_id:id}).then(function(resp){ aow_showModal('Retry Job', '<div>' + (resp.message || JSON.stringify(resp)) + '</div>', '<button id="aow-admin-modal-ok" class="button button-primary">OK</button>'); setTimeout(function(){ location.reload(); }, 1200); }).catch(function(e){ aow_showModal('Retry Failed', '<div>Retry failed: '+escHtml(e.message)+'</div>'); setTimeout(function(){ location.reload(); }, 1200); }); }); });

        document.querySelectorAll('.aow-purge-job').forEach(function(b){ b.addEventListener('click', function(){ var id=this.getAttribute('data-jobid'); if(!id) return; var btn=this; aow_confirm('Purge this job?', function(){ btn.disabled=true; fetchJson(window.AOW_REST.root + 'purge-job', {job_id:id}).then(function(r){ aow_showModal('Purge Job', '<div>' + (r.message || JSON.stringify(r)) + '</div>', '<button id="aow-admin-modal-ok" class="button button-primary">OK</button>'); setTimeout(function(){ location.reload(); }, 1200); }).catch(function(e){ aow_showModal('Purge Failed', '<div>Purge failed: '+escHtml(e.message)+'</div>'); setTimeout(function(){ location.reload(); }, 1200); }); }, function(){ /* cancelled */ }); }); });

        document.querySelectorAll('.aow-details-job').forEach(function(b){ b.addEventListener('click', function(){ var id=this.getAttribute('data-jobid'); if(!id) return; var row = this.closest('tr'); var resCell = row.querySelector('td:nth-child(7)'); var details = resCell ? resCell.innerHTML : 'No details'; aow_showModal('Job Details '+id, '<div style="max-height:60vh;overflow:auto">'+details+'</div>', '<button id="aow-admin-modal-ok" class="button button-primary">Close</button>'); }); });
    }

    // Initialize when DOM ready
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initJobButtons); else initJobButtons();
})();
