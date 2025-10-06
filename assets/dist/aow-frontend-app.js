(()=>{var n={currentView:"generator",isAdminLoggedIn:!1,certificates:{},baseVerificationUrl:window.location.href.split("?")[0]+"?view=verifier&id="};try{let t=localStorage.getItem("aow_preview_certificates");t&&(n.certificates=JSON.parse(t))}catch(t){console.warn("aow preview: failed to restore certificates",t)}var g=document.getElementById("content-area"),d=document.getElementById("message-box"),u=document.getElementById("nav-generator"),p=document.getElementById("nav-verifier");function m(t,e="info"){let o="bg-aow-primary";e==="success"&&(o="bg-green-500"),e==="error"&&(o="bg-red-600"),d.className=`fixed top-0 right-0 m-6 p-4 rounded-lg shadow-2xl text-white transition-opacity duration-300 z-50 pointer-events-none ${o}`,d.textContent=t,d.style.opacity=1,setTimeout(()=>{d.style.opacity=0},3e3)}function h(){return"AOWL-"+Math.random().toString(36).substring(2,8).toUpperCase()+"-"+Date.now().toString().slice(-4)}function f(t){let e="text-aow-primary border border-aow-primary hover:bg-aow-card-bg/70",o="bg-aow-primary text-aow-dark-bg shadow-lg hover:shadow-aow-glow";u.className="px-5 py-2 text-sm font-semibold rounded-full transition duration-300 ml-3",p.className="px-5 py-2 text-sm font-semibold rounded-full transition duration-300 ml-3",t==="generator"&&n.isAdminLoggedIn?(u.classList.add(...o.split(" ")),p.classList.add(...e.split(" "))):(p.classList.add(...o.split(" ")),u.classList.add(...e.split(" ")))}function x(){n.isAdminLoggedIn=!1,f("verifier"),g.innerHTML=`
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
        `,document.getElementById("admin-login-form").addEventListener("submit",E)}function b(){f("generator"),g.innerHTML=`
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
                        <div class="flex space-x-2">
                            <input type="url" id="logo-url" value="" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="URL for logo image">
                            <button type="button" id="upload-logo-btn" class="px-3 py-2 bg-aow-primary text-aow-dark-bg rounded-md">Upload</button>
                        </div>
                        <input type="file" id="logo-file-input" accept="image/*" style="display:none">
                        <p class="text-xs text-gray-400 mt-1">You can upload or paste a logo URL. Upload uses client-side file conversion in the preview.</p>
                    </div>

                    <div class="col-span-1">
                        <label for="signature-url" class="block text-sm font-medium text-gray-300 mb-1">Signature Image URL</label>
                        <div class="flex space-x-2">
                            <input type="url" id="signature-url" value="" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="URL for signature image (optional)">
                            <button type="button" id="upload-signature-btn" class="px-3 py-2 bg-aow-primary text-aow-dark-bg rounded-md">Upload</button>
                        </div>
                        <input type="file" id="signature-file-input" accept="image/*" style="display:none">
                        <p class="text-xs text-gray-400 mt-1">Upload a signature image or provide its URL.</p>
                    </div>

                    <div class="col-span-1 lg:col-span-3 mt-6">
                        <button type="submit" class="w-full py-4 bg-green-600 text-white font-bold text-xl rounded-xl hover:bg-green-500 transition duration-300 shadow-lg hover:shadow-green-500/50 transform hover:scale-[1.01]">
                            GENERATE UNIQUE CERTIFICATE & SAVE
                        </button>
                    </div>
                </form>

                <div class="mt-10 pt-6 border-t border-aow-primary/50">
                    <h3 class="text-2xl font-bold text-aow-primary mb-4">Generated Certificates List (${Object.keys(n.certificates).length})</h3>
                    <div id="certificate-list" class="space-y-4">
                        ${Object.keys(n.certificates).length===0?'<p class="text-gray-500 italic">No certificates generated yet. Start by filling the form above.</p>':""}
                    </div>
                </div>
            </div>
        `,document.getElementById("certificate-form").addEventListener("submit",k),I();let t=document.getElementById("upload-logo-btn"),e=document.getElementById("logo-file-input");t&&t.addEventListener("click",()=>e.click()),e&&e.addEventListener("change",l=>{let r=l.target.files[0];if(!r)return;let s=new FileReader;s.onload=a=>{document.getElementById("logo-url").value=a.target.result},s.readAsDataURL(r)});let o=document.getElementById("upload-signature-btn"),i=document.getElementById("signature-file-input");o&&i&&o.addEventListener("click",()=>i.click()),i&&i.addEventListener("change",l=>{let r=l.target.files[0];if(!r)return;let s=new FileReader;s.onload=a=>document.getElementById("signature-url").value=a.target.result,s.readAsDataURL(r)})}function I(){let t=document.getElementById("certificate-list");if(t){if(Object.keys(n.certificates).length===0){t.innerHTML='<p class="text-gray-500 italic">No certificates generated yet. Start by filling the form above.</p>';return}t.innerHTML=Object.values(n.certificates).map(e=>`
            <div class="flex flex-col sm:flex-row justify-between items-center p-4 bg-aow-input-bg rounded-lg border border-aow-card-bg/50 hover:border-aow-secondary transition duration-300 cursor-default shadow-lg">
                <div>
                    <p class="font-semibold text-xl text-gray-100">${e.studentName}</p>
                    <p class="text-sm text-gray-400">${e.courseTitle} | ID: <span class="text-aow-secondary font-mono text-sm">${e.certificateId}</span></p>
                </div>
                <div class="mt-3 sm:mt-0 flex space-x-2">
                    <button onclick="showCertificate('${e.certificateId}')" class="px-3 py-1 text-sm bg-aow-secondary text-aow-dark-bg font-semibold rounded-full hover:bg-aow-primary hover:text-white transition shadow-md">View Certificate</button>
                    <button onclick="deleteCertificate('${e.certificateId}')" class="px-3 py-1 text-sm bg-red-700 text-red-100 rounded-full hover:bg-red-600 transition">Delete</button>
                </div>
            </div>
        `).join("")}}function w(t=null){f("verifier"),g.innerHTML=`
            <div class="p-8 md:p-12 rounded-3xl shadow-strong illuminated-card max-w-lg mx-auto">
                <h2 class="text-3xl font-bold text-center text-aow-primary mb-4">Certificate Verification Portal</h2>
                <p class="text-center text-gray-400 mb-8">Enter the unique Certificate ID or scan the QR code to verify validity.</p>
                
                <form id="verification-form" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" id="verify-id" value="${t||""}" required class="flex-grow px-4 py-3 border rounded-lg focus:ring-2 focus:ring-aow-primary" placeholder="Enter Certificate ID (e.g., AOWL-XXXXXX-XXXX)">
                    <button type="submit" class="shrink-0 px-6 py-3 bg-aow-primary text-aow-dark-bg font-bold rounded-lg hover:bg-aow-secondary transition duration-300 shadow-md hover:shadow-aow-glow">
                        Verify Certificate
                    </button>
                </form>
                
                <div id="verification-result" class="mt-8 pt-6 border-t border-aow-primary/50 min-h-24">
                    <p class="text-center text-gray-500">Enter a Certificate ID and click 'Verify' to check status.</p>
                </div>
            </div>
        `,document.getElementById("verification-form").addEventListener("submit",y),t&&y({preventDefault:()=>{}})}function L(t){let e=n.certificates[t];if(!e)return m("Certificate not found!","error");let o=document.createElement("div");o.id="certificate-modal",o.className="fixed inset-0 bg-aow-dark-bg bg-opacity-95 flex items-center justify-center p-4 z-50",o.innerHTML=`
            <div class="relative bg-aow-cert-bg text-gray-200 p-8 rounded-xl max-w-4xl w-full h-auto overflow-y-auto certificate-container">
                <button onclick="document.getElementById('certificate-modal').remove()" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-aow-secondary transition rounded-full hover:bg-aow-card-bg/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="p-8 md:p-16 text-center">
                    <img src="${e.logoUrl}" alt="AppsOrWebs Logo" class="mx-auto mb-8 h-12">
                    
                    <h3 class="text-xl font-medium text-gray-400 uppercase mb-2">Certificate of Completion</h3>
                    <h2 class="text-5xl font-extrabold text-aow-primary mb-6">AppsOrWebs Limited</h2>

                    <p class="text-2xl font-light italic mb-8">This certifies that</p>
                    
                    <p class="text-6xl font-serif font-bold text-gray-50 border-b-4 border-aow-secondary inline-block px-10 pb-2 mb-12">${e.studentName}</p>
                    
                    <p class="text-2xl font-light italic mb-2">has successfully completed the demanding curriculum for the course:</p>
                    <p class="text-4xl font-bold text-gray-50 mb-10">${e.courseTitle}</p>
                    
                    <div class="flex flex-col md:flex-row justify-center items-center mt-12 space-y-8 md:space-y-0 md:space-x-16">
                        
                        <div class="flex-1 text-center max-w-xs">
                            <img src="${e.signatureUrl}" alt="Signature" class="h-10 mx-auto mb-2 filter-none" style="opacity: 0.8;">
                            <div class="border-t border-gray-600 pt-1">
                                <p class="text-lg font-semibold text-gray-100">${e.instructorName}</p>
                                <p class="text-sm text-gray-400">Authorized Instructor (optional)</p>
                            </div>
                        </div>
                        
                        <div class="text-center max-w-xs">
                            <p class="text-2xl font-bold text-gray-50">${e.completionDate}</p>
                            <div class="border-t border-gray-600 pt-1">
                                <p class="text-sm text-gray-400">Date of Completion</p>
                            </div>
                        </div>

                        <div class="flex-1 text-center max-w-xs">
                            <div id="qrcode-${t}" class="mx-auto mb-2 p-2 border-2 border-aow-primary inline-block"></div>
                            <p class="text-xs font-mono break-words text-gray-200">${e.certificateId}</p>
                            <p class="text-sm text-gray-400 mt-1">Scan or use ID for verification</p>
                        </div>
                    </div>

                </div>
                <p class="text-center text-xs text-gray-500 mt-8">VERIFIED LINK: ${n.baseVerificationUrl+t}</p>

            </div>
        `,document.body.appendChild(o),new QRCode(document.getElementById(`qrcode-${t}`),{text:n.baseVerificationUrl+t,width:80,height:80,colorDark:"#FFFFFF",colorLight:"#2F3C50",correctLevel:QRCode.CorrectLevel.H});let i=document.getElementById("certificate-modal"),l=document.createElement("div");l.className="mt-6 flex justify-center space-x-3",l.innerHTML=`
            <button id="download-json" class="px-3 py-2 bg-gray-700 rounded text-sm">Download JSON</button>
            <button id="download-svg" class="px-3 py-2 bg-gray-700 rounded text-sm">Download SVG</button>
            <button id="download-png" class="px-3 py-2 bg-gray-700 rounded text-sm">Download PNG</button>
            <button id="download-pdf" class="px-3 py-2 bg-green-600 rounded text-sm">Download PDF</button>
        `,i.querySelector(".p-8").appendChild(l),document.getElementById("download-json").addEventListener("click",()=>{let r=new Blob([JSON.stringify(e,null,2)],{type:"application/json"}),s=URL.createObjectURL(r),a=document.createElement("a");a.href=s,a.download=`${e.certificateId}.json`,a.click(),URL.revokeObjectURL(s)}),document.getElementById("download-svg").addEventListener("click",()=>{let r=`<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><foreignObject width="100%" height="100%"><div xmlns="http://www.w3.org/1999/xhtml" style="font-family:Inter,sans-serif;background:${getComputedStyle(document.querySelector(".certificate-container")).backgroundColor};color:#fff;padding:40px;"><h1 style="font-size:36px;color:${getComputedStyle(document.querySelector(".aow-header-glow")).color||"#00C2B2"}">Certificate</h1><h2 style="font-size:48px">${e.studentName}</h2><p style="font-size:24px">${e.courseTitle}</p></div></foreignObject></svg>`,s=new Blob([r],{type:"image/svg+xml"}),a=URL.createObjectURL(s),c=document.createElement("a");c.href=a,c.download=`${e.certificateId}.svg`,c.click(),URL.revokeObjectURL(a)}),document.getElementById("download-png").addEventListener("click",async()=>{let r=i.querySelector(".certificate-container");try{let a=(await html2canvas(r,{backgroundColor:null,scale:2})).toDataURL("image/png"),c=document.createElement("a");c.href=a,c.download=`${e.certificateId}.png`,c.click()}catch(s){console.error("html2canvas failed",s),m("PNG export failed in preview. See console for details.","error")}}),document.getElementById("download-pdf").addEventListener("click",()=>{let r=window.open("","_blank");r.document.write(i.innerHTML),r.document.close(),r.focus(),r.print()})}function E(t){t.preventDefault(),n.isAdminLoggedIn=!0,m("Preview login successful.","success"),b()}function k(t){t.preventDefault();let e=document.getElementById("student-name").value,o=document.getElementById("course-title").value,i=document.getElementById("completion-date").value,l=document.getElementById("instructor-name").value,r=document.getElementById("logo-url").value,s=document.getElementById("signature-url").value,a=h(),c={certificateId:a,studentName:e,courseTitle:o,completionDate:i,instructorName:l,logoUrl:r,signatureUrl:s,verificationUrl:n.baseVerificationUrl+a};n.certificates[a]=c;try{localStorage.setItem("aow_preview_certificates",JSON.stringify(n.certificates))}catch(v){console.warn("persist failed",v)}m(`Certificate for ${e} generated successfully! ID: ${a}`,"success"),b(),L(a)}function y(t){t.preventDefault();let e=document.getElementById("verification-result"),o=document.getElementById("verify-id").value.trim().toUpperCase(),i=n.certificates[o];i?e.innerHTML=`
                <div class="p-6 rounded-xl border-4 border-aow-primary bg-aow-card-bg shadow-inner-glow">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-aow-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-2xl font-bold text-aow-primary">CERTIFICATE VERIFIED & VALID</h3>
                    </div>
                    <p class="mb-2"><span class="font-bold text-aow-secondary">Student Name:</span> ${i.studentName}</p>
                    <p class="mb-2"><span class="font-bold text-aow-secondary">Course Completed:</span> ${i.courseTitle}</p>
                    <p class="mb-2"><span class="font-bold text-aow-secondary">Completion Date:</span> ${i.completionDate}</p>
                    <p class="mb-0"><span class="font-bold text-aow-secondary">Certificate ID:</span> <span class="font-mono">${i.certificateId}</span></p>
                </div>
            `:e.innerHTML=`
                <div class="p-6 rounded-xl border-4 border-red-500 bg-aow-card-bg shadow-md">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-2xl font-bold text-red-400">VERIFICATION FAILED</h3>
                    </div>
                    <p>The Certificate ID '<span class="font-mono font-bold">${o||"EMPTY"}</span>' was not found in our records. Please ensure the ID is correct or contact AppsOrWebs Limited support.</p>
                </div>
            `}function C(){let t=new URLSearchParams(window.location.search),e=t.get("view"),o=t.get("id");u.addEventListener("click",()=>{n.isAdminLoggedIn?b():x()}),p.addEventListener("click",()=>w()),e==="verifier"?(n.currentView="verifier",w(o)):(n.currentView="generator",x())}window.onload=C;})();
//# sourceMappingURL=aow-frontend-app.js.map
