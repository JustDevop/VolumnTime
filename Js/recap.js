function enableEdit(fieldId) {
    if (fieldId === 'interets') {
        var checkboxes = document.querySelectorAll('.centre1 input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.disabled = false;
            checkbox.classList.remove('readonly');
        });
    } else {
        var field = document.getElementById(fieldId);
        if (field.tagName === 'SELECT' || field.type === 'checkbox') {
            field.disabled = false;
            field.classList.remove('readonly');
        } else {
            field.readOnly = false;
            field.classList.remove('readonly');
        }
    }
}

function toggleHandicapField() {
    var handicapField = document.getElementById('handicap_field');
    var descriptionHandicap = document.getElementById('description_handicap');
    if (document.getElementById('handicap_checkbox').checked) {
        handicapField.style.display = 'block';
        descriptionHandicap.readOnly = false;
        descriptionHandicap.classList.remove('readonly');
    } else {
        handicapField.style.display = 'none';
        descriptionHandicap.readOnly = true;
        descriptionHandicap.classList.add('readonly');
    }
}