//mostly messages are acting as comments
//so we can say that code is self explainatery
function validatePassword(passid){
    let password = document.getElementById(passid);
    let msg = "";
    if(password.value.length < 8)
        msg = "Password length can't be less than 8!";
    else if(!password.value.match(/[0-9]/)) //password must contain a digit
        msg = "Password must contain a digit!";
    else if(!password.value.match(/[!@#$%^&*.]/)) //password must contain a special character
        msg = "Password must contain a special characer!";
    password.setCustomValidity(msg);
}

function confirmPassword(passid, pass2id){
    let pass = document.getElementById(passid);
    let pass2 = document.getElementById(pass2id);
    pass2.setCustomValidity(pass.value == pass2.value ? "" : "Passwords don't match!");
}

function validateEmail(email_id, user_type){
    let email = document.getElementById(email_id);
    
    //regex matching
    if(user_type == 2)
        email.setCustomValidity(email.value.match(/@dmu\.ac\.uk$/)? "": "Email must end with @dmu.ac.uk");
    else
        email.setCustomValidity(email.value.match(/@my365\.dmu\.ac\.uk$/)? "": "Email must end with @my365.dmu.ac.uk");

}

function validateName(name_id){
    let name = document.getElementById(name_id);
    //regex matching to exlude numbers
    name.setCustomValidity(name.value.match(/^[a-zA-Z ]+$/)? "" : "Name must contain alphabets only");
}

function openPopupForm(form_id){
    let form = document.getElementById(form_id);
    form.style.display = "block";
}

function openReplyPopupForm(form_id, rtor_input_id, reply_id){
    openPopupForm(form_id);
    let rtor = document.getElementById(rtor_input_id);
    rtor.value = reply_id;
}


function closePopupForm(form_id){
    let form = document.getElementById(form_id);
    form.style.display = "none";
}

function toggleCssDisplay(e_id){  //pass the element id to toggle function
    let element = document.getElementById(e_id);
    let currentDisplay = element.style.display;
    element.style.display = (currentDisplay == "none")? "block" : "none"; 
}

if ( window.history.replaceState ) {
    //resubmission of form is not allowed
    //so if refresh button is re-pressed, clear the link
    window.history.replaceState( null, null, window.location.href );
}
