function confirmerSuppression(){
    let nomPage = document.querySelector('input[name="_pageNom"]').value;
    return confirm('Etes-vous sûr de vouloir supprimer la page "'+ nomPage +'" ?'); 
}

  async function updatePreview() {
    var selectedPageId = document.getElementById("_pageId").value;
    var iframe = document.getElementById("template-preview");
    iframe.src = "template.php?pageId=" + selectedPageId;
  }
  document.getElementById("saveButton").addEventListener("click", updatePreview);

