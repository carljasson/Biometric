<!DOCTYPE html>
<html>
<head>
    <title>Step 1 - Personal Info</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: url('/images/background.png') no-repeat center center fixed; background-size: cover; margin:0; height:100vh; display:flex; justify-content:center; align-items:start; }
        .progress-container { position: fixed; top:0; left:0; height:6px; width:100%; background:#e0e0e0; z-index:1000; }
        .progress-bar { height:100%; width:0%; background-color:#007bff; transition: width 0.3s ease; }
        .progress-percentage { position:absolute; top:10px; left:50%; transform:translateX(-50%); font-size:30px; color:#007bff; }
        .top-left-back { position: fixed; top:10px; left:10px; font-size:16px; color:#007bff; background-color:#fff; padding:8px 14px; border-radius:10px; text-decoration:none; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:9999; }
        .form-container { background: rgba(255,255,255,0.96); padding:40px 30px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.15); max-width:460px; width:100%; margin-top:100px; }
        h2 { text-align:center; margin-bottom:25px; color:#2c3e50; }
        input, select { width:100%; padding:12px; margin:8px 0; border:1px solid #ccc; border-radius:10px; font-size:15px; }
        .toggle-label { font-size:12px; cursor:pointer; color:#007bff; display:inline-block; margin-top:-5px; margin-bottom:10px; }
        button { width:100%; background-color:#007bff; color:white; padding:12px; border:none; border-radius:10px; cursor:pointer; font-size:16px; }
        .form-step { display:none; }
        .form-step.active { display:block; }
        .btn-group { display:flex; justify-content:space-between; gap:10px; margin-top:20px; }
        @media(max-width:480px){ .form-container{padding:25px 20px; margin-top:90px;} .top-left-back{font-size:14px;padding:6px 10px;} }
    </style>
</head>
<body>
<div class="progress-container">
    <div class="progress-bar" id="progressBar"></div>
    <div class="progress-percentage" id="progressPercentage">0%</div>
</div>

<a href="#" class="top-left-back" id="topBackBtn">&larr; Back</a>

<div class="form-container">
    <h2>Step 1: Registration</h2>
    <form method="POST" action="{{ route('register.step1') }}" id="step1Form">
        @csrf

        <!-- Step 1 -->
        <div class="form-step active">
            <input type="text" name="firstname" placeholder="First Name" required oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
            <input type="text" name="middlename" placeholder="Middle Name" oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
            <input type="text" name="lastname" placeholder="Last Name" required oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <!-- Step 2 -->
        <div class="form-step">
            <input type="date" name="birthday" id="birthday" required>
            <input type="number" name="age" id="age" placeholder="Age" readonly required>
        </div>

        <!-- Step 3 -->
        <div class="form-step">
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <select name="status" required>
                <option value="">Select Civil Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
            </select>
        </div>

        <!-- Step 4 -->
        <div class="form-step">
            <input type="text" name="phone" id="phone" placeholder="Phone Number (e.g. 09123456789)" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <input type="text" name="address" placeholder="Address" required>
        </div>

        <!-- Step 5 -->
        <div class="form-step">
            <input type="text" name="contact_name" placeholder="Emergency Contact Name" required oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
            <input type="text" name="contact_number" id="contact_number" placeholder="Contact Number (e.g. 09123456789)" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <!-- Step 6 -->
        <div class="form-step">
            <input type="email" name="email" id="email" placeholder="Email" required>
            <div>
                <input type="password" name="password" id="password" placeholder="Password" minlength="8" maxlength="16" required>
                <label class="toggle-label" onclick="togglePassword('password',this)">Show Password</label>
            </div>
            <div>
                <input type="password" name="password_confirmation" id="confirm_password" placeholder="Confirm Password" minlength="8" maxlength="16" required>
                <label class="toggle-label" onclick="togglePassword('confirm_password',this)">Show Password</label>
            </div>

            <!-- Terms of Use / Privacy Policy -->
            <div style="margin-top:10px;">
                <input type="checkbox" id="termsCheckbox" required>
                <label for="termsCheckbox">
                    I agree to the <a href="#" id="showTerms">Terms of Use</a> and <a href="#" id="showPrivacy">Privacy Policy</a>
                </label>
            </div>
        </div>

        <div class="btn-group">
            <button type="button" id="nextBtn">Next</button>
            <button type="submit" id="submitBtn" style="display:none;">Submit</button>
        </div>
    </form>
</div>

<!-- Modal -->
<div id="termsModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center; z-index:9999;">
    <div style="background:#fff;padding:30px;border-radius:15px;max-width:500px;width:90%;position:relative;">
        <span id="closeModal" style="position:absolute;top:10px;right:15px;cursor:pointer;font-size:20px;">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

<script>
const steps = document.querySelectorAll('.form-step');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
const topBackBtn = document.getElementById('topBackBtn');
const progressBar = document.getElementById('progressBar');
const progressPercentage = document.getElementById('progressPercentage');
let currentStep = 0;

// Show step and progress
function showStep(step){
    steps.forEach((s,i)=>s.classList.toggle('active',i===step));
    nextBtn.style.display = step<steps.length-1?'inline-block':'none';
    submitBtn.style.display = step===steps.length-1?'inline-block':'none';
    const percent=Math.round(((step+1)/steps.length)*100);
    progressBar.style.width=percent+'%';
    progressPercentage.textContent=percent+'%';
}

// Toggle password visibility
function togglePassword(id,label){
    const input = document.getElementById(id);
    input.type = input.type==='password' ? 'text' : 'password';
    label.textContent = input.type==='text' ? 'Hide Password' : 'Show Password';
}

// Next button click
nextBtn.addEventListener('click', async ()=>{
    const inputs = steps[currentStep].querySelectorAll('input,select');
    let valid = true;

    inputs.forEach(input => {
        if(input.required && input.value.trim() === ''){
            valid = false;
            input.style.borderColor = 'red';
        } else { 
            input.style.borderColor = '#ccc'; 
        }
    });

    if(!valid){
        Swal.fire({icon:'error',title:'Oops...',text:'Please complete all required fields!'});
        return;
    }

    // Step 4: Check phone uniqueness
    if(currentStep === 3){
        const phone = document.getElementById('phone').value.trim();
        const res = await fetch('{{ route("check.phone") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ phone })
        });
        const data = await res.json();
        if(data.exists){
            Swal.fire({icon:'error',title:'Oops...',text:'Phone number is already in use!'});
            return;
        }
    }

    // Step 6: Password strength check
    if(currentStep === steps.length-1){
        const password = document.getElementById('password').value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,16}$/;
        if(!regex.test(password)){
            Swal.fire({
                icon:'error',
                title:'Weak Password',
                html:'Password must contain:<br>- 1 uppercase letter<br>- 1 lowercase letter<br>- 1 number<br>- 1 special character<br>- 8-16 characters'
            });
            return;
        }
    }

    currentStep++;
    showStep(currentStep);
});

// Back button
topBackBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    if(currentStep===0) window.location.href='/';
    else { currentStep--; showStep(currentStep); }
});

// Birthday -> Age
document.getElementById('birthday').addEventListener('change', function(){
    const birthdate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    const m = today.getMonth() - birthdate.getMonth();
    if(m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) age--;
    document.getElementById('age').value = age>0 ? age : '';
});

// Max birthday = today
const now = new Date();
document.getElementById('birthday').setAttribute('max',
    `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`
);

// Real-time email uniqueness check
document.getElementById('email').addEventListener('blur', async function(){
    const email = this.value.trim();
    if(email){
        const res = await fetch('{{ route("check.email") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ email })
        });
        const data = await res.json();
        if(data.exists){
            Swal.fire({icon:'error',title:'Oops...',text:'Email is already in use!'});
            this.value = '';
        }
    }
});

// Terms modal logic
const termsModal = document.getElementById('termsModal');
const modalContent = document.getElementById('modalContent');
const closeModal = document.getElementById('closeModal');

document.getElementById('showTerms').addEventListener('click', (e)=>{
    e.preventDefault();
    modalContent.innerHTML = `
        <h3>Terms of Use</h3>
        <p>Your use of this service is subject to our terms. We handle your personal information securely and only use it for registration purposes.</p>
    `;
    termsModal.style.display = 'flex';
});

document.getElementById('showPrivacy').addEventListener('click', (e)=>{
    e.preventDefault();
    modalContent.innerHTML = `
        <h3>Privacy Policy</h3>
        <p>We collect your personal data for registration and communication purposes only. Your data will not be shared with third parties without your consent.</p>
    `;
    termsModal.style.display = 'flex';
});

closeModal.addEventListener('click', ()=>termsModal.style.display='none');
termsModal.addEventListener('click', (e)=>{ if(e.target === termsModal) termsModal.style.display='none'; });

// Validate terms checkbox on submit
document.getElementById('step1Form').addEventListener('submit', function(e){
    const termsCheckbox = document.getElementById('termsCheckbox');
    if(!termsCheckbox.checked){
        e.preventDefault();
        Swal.fire({
            icon:'warning',
            title:'Agreement Required',
            text:'You must agree to the Terms of Use and Privacy Policy to register.'
        });
        return false;
    }
});

showStep(currentStep);
</script>

</body>
</html>
