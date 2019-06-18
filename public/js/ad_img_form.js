/*-- ad_img_form VA GERER L'IMBRICATION DES FORMULAIRE D'IMAGE LORS DE LA CREATION D'UNE ANNONCE --*/

$('#add-image').click(function(){
    //récupération du nombre de div form-groupe dans la main div ad-images
    const index = +$('#widgets-counter').val();

    //récupération du prototype des entrées en remplacant le __name__ par l'index (g permet de dire qu'on va le faire plusieurs fois)
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    //injection du code dans la div
    $('#ad_images').append(tmpl)

    //incrémentation du la value de widgets-counter
    $('#widgets-counter').val(index + 1);

    //bouton suppr
    handleDeleteButtons();
});

function updateCounter(){
    const count = +$('#ad_images div.form-group').length;

    $('#widhets-counter').val(count);
}

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        //this représente le button
        //dataset tous ses attributs (data-)
        //target pour accéder à l'attr data-target
        const target = this.dataset.target;
        $(target).remove();
    });
}
//Appel au chargement de la page
updateCounter();
handleDeleteButtons();
