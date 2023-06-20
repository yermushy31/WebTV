function confirmerSuppression(){
    let nomPage = document.querySelector('input[name="_pageNom"]').value;
    return confirm('Etes-vous sûr de vouloir supprimer la page "'+ nomPage +'" ?'); 
}
