function toggleHandicapField() {
    var checkbox = document.getElementById('handicap_checkbox');
    var handicapField = document.getElementById('handicap_field');
    if (checkbox.checked) {
        handicapField.style.display = 'block';
    } else {
        handicapField.style.display = 'none';
    }
}
