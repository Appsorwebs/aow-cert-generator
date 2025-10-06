(()=>{(function(){"use strict";function g(r){document.readyState!=="loading"?r():document.addEventListener("DOMContentLoaded",r)}g(function(){console.log("AOW Certificate Generator: Initializing...");let r=document.getElementById("content-area"),s=document.getElementById("message-box"),n=document.getElementById("nav-generator"),a=document.getElementById("nav-verifier");if(!r){console.error("AOW: content-area element not found");return}let i={currentView:"generator",isAdminLoggedIn:typeof AOW_REST<"u"&&AOW_REST.isAdmin||!1,certificates:{},baseVerificationUrl:window.location.href.split("?")[0]+"?view=verifier&id="};console.log("AOW: State initialized",{isAdmin:i.isAdminLoggedIn});function l(e,t="info"){if(!s)return;let o="bg-blue-600";t==="success"&&(o="bg-green-500"),t==="error"&&(o="bg-red-600"),s.className=`fixed top-0 right-0 m-6 p-4 rounded-lg shadow-2xl text-white transition-opacity duration-300 z-50 pointer-events-none ${o}`,s.textContent=e,s.style.opacity=1,setTimeout(()=>{s.style.opacity=0},3e3)}function v(e){return!e&&e!==0?"":String(e).replace(/[&<>"]/g,function(t){return{"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[t]})}function y(){return"AOWL-"+Math.random().toString(36).substring(2,8).toUpperCase()+"-"+Date.now().toString().slice(-4)}function d(e){if(!n||!a)return;let t="text-aow-primary border border-aow-primary hover:bg-aow-card-bg/70",o="bg-aow-primary text-aow-dark-bg shadow-lg hover:shadow-aow-glow";n.className="px-5 py-2 text-sm font-semibold rounded-full transition duration-300 ml-3",a.className="px-5 py-2 text-sm font-semibold rounded-full transition duration-300 ml-3",e==="generator"&&i.isAdminLoggedIn?(n.classList.add(...o.split(" ")),a.classList.add(...t.split(" "))):(a.classList.add(...o.split(" ")),n.classList.add(...t.split(" ")))}function u(){console.log("AOW: Rendering login view"),i.isAdminLoggedIn=!1,d("verifier"),r.innerHTML=`
                <div class="p-8 md:p-12 rounded-3xl shadow-strong illuminated-card max-w-md mx-auto">
                    <h2 class="text-3xl font-bold text-center text-aow-primary mb-6">Admin Access: Generator Login</h2>
                    <p class="text-center text-gray-400 mb-8">Access is restricted to authorized AppsOrWebs Limited personnel.</p>
                    <form id="admin-login-form">
                        <label for="admin-pass" class="block text-sm font-medium text-gray-300 mb-2">Secure Password</label>
                        <input type="password" id="admin-pass" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary transition duration-300" placeholder="Enter Admin Password">
                        <button type="submit" class="w-full mt-6 py-3 bg-aow-secondary text-aow-dark-bg font-bold rounded-lg hover:bg-aow-primary hover:text-white transition duration-300 shadow-md hover:shadow-secondary-glow">
                            Authenticate & Login
                        </button>
                    </form>
                </div>
            `;let e=document.getElementById("admin-login-form");e&&e.addEventListener("submit",f)}function c(){console.log("AOW: Rendering admin view"),d("generator"),r.innerHTML=`
                <div class="p-6 md:p-10 rounded-3xl shadow-strong illuminated-card">
                    <h2 class="text-3xl font-bold text-aow-primary mb-8 border-b border-aow-secondary/50 pb-4">Certificate Generation Portal</h2>
                    
                    <form id="certificate-form" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        <div class="lg:col-span-3">
                            <h3 class="text-2xl font-bold mb-4 text-aow-secondary border-b border-aow-card-bg/50 pb-2">Student & Course Details</h3>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="student-name" class="block text-sm font-medium text-gray-300 mb-1">Student Name (Full)</label>
                            <input type="text" id="student-name" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="e.g., Jane E. Doe">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="course-title" class="block text-sm font-medium text-gray-300 mb-1">Course Title</label>
                            <input type="text" id="course-title" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="e.g., Advanced Full-Stack Development">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="completion-date" class="block text-sm font-medium text-gray-300 mb-1">Completion Date</label>
                            <input type="date" id="completion-date" value="${new Date().toISOString().split("T")[0]}" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary bg-aow-input-bg text-gray-200">
                        </div>
                        
                        <div class="lg:col-span-3 mt-4">
                            <h3 class="text-2xl font-bold mb-4 text-aow-secondary border-b border-aow-card-bg/50 pb-2">Branding & Authorization</h3>
                        </div>

                        <div class="col-span-1">
                            <label for="instructor-name" class="block text-sm font-medium text-gray-300 mb-1">Instructor/Signatory Name <span class="text-xs text-gray-400">(optional)</span></label>
                            <input type="text" id="instructor-name" value="Michael Anderson, CEO AppsOrWebs Limited" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary">
                        </div>

                        <div class="col-span-1">
                            <label for="logo-url" class="block text-sm font-medium text-gray-300 mb-1">AppsOrWebs Logo URL</label>
                            <input type="url" id="logo-url" value="" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="URL for logo image">
                        </div>

                        <div class="col-span-1">
                            <label for="signature-url" class="block text-sm font-medium text-gray-300 mb-1">Signature Image URL</label>
                            <input type="url" id="signature-url" value="" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="URL for signature (optional)">
                        </div>

                        <div class="col-span-1 lg:col-span-3 mt-6">
                            <button type="submit" class="w-full py-4 bg-green-600 text-white font-bold text-xl rounded-xl hover:bg-green-500 transition duration-300 shadow-lg hover:shadow-green-500/50 transform hover:scale-[1.01]">
                                GENERATE UNIQUE CERTIFICATE & SAVE
                            </button>
                        </div>
                    </form>

                    <div class="mt-10 pt-6 border-t border-aow-primary/50">
                        <h3 class="text-2xl font-bold text-aow-primary mb-4">Generated Certificates</h3>
                        <div id="certificate-list" class="space-y-4">
                            <p class="text-gray-500 italic">No certificates generated yet.</p>
                        </div>
                    </div>
                </div>
            `;let e=document.getElementById("certificate-form");e&&e.addEventListener("submit",p)}function m(e=null){console.log("AOW: Rendering verification view"),d("verifier"),r.innerHTML=`
                <div class="p-8 md:p-12 rounded-3xl shadow-strong illuminated-card max-w-lg mx-auto">
                    <h2 class="text-3xl font-bold text-center text-aow-primary mb-4">Certificate Verification Portal</h2>
                    <p class="text-center text-gray-400 mb-8">Enter the unique Certificate ID to verify validity.</p>
                    
                    <form id="verification-form" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="verify-id" value="${e||""}" required class="flex-grow px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="Enter Certificate ID">
                        <button type="submit" class="shrink-0 px-6 py-3 bg-aow-primary text-aow-dark-bg font-bold rounded-lg hover:bg-aow-secondary transition duration-300">
                            Verify
                        </button>
                    </form>
                    
                    <div id="verification-result" class="mt-8 pt-6 border-t border-aow-primary/50 min-h-24">
                        <p class="text-center text-gray-500">Enter a Certificate ID to verify.</p>
                    </div>
                </div>
            `;let t=document.getElementById("verification-form");t&&t.addEventListener("submit",b)}function f(e){e.preventDefault(),console.log("AOW: Login attempt, isAdmin:",i.isAdminLoggedIn),i.isAdminLoggedIn?(l("Login Successful!","success"),c()):l("Please log in as WordPress administrator first.","error")}function p(e){e.preventDefault(),l("Certificate generation - coming soon!","info")}function b(e){e.preventDefault(),l("Certificate verification - coming soon!","info")}function x(){console.log("AOW: initializeApp called");let e=new URLSearchParams(window.location.search),t=e.get("view"),o=e.get("id");n&&n.addEventListener("click",()=>{i.isAdminLoggedIn?c():u()}),a&&a.addEventListener("click",()=>m()),t==="verifier"?(i.currentView="verifier",m(o)):(i.currentView="generator",i.isAdminLoggedIn?c():u()),console.log("AOW: Initialization complete")}x()})})();})();
//# sourceMappingURL=aow-frontend-app.js.map
