const upvoteBtns = document.querySelectorAll("[name='upvoteBtn']")                  //Minden poszthoz generálódik egy upvote gomb, ami data_postID-ként eltárolja a poszt azonosítóját
upvoteBtns.forEach(upvoteBtn => upvoteBtn.addEventListener('click', upvotePost))    //Minden Upvote gombhoz hozzáadjuk az eseménykezelőt.

async function upvotePost(){

    //AJAX-szal elküldesz egy GET kérést az upvote-olást intéző php szkript-nek felparaméterezve
    const request = await fetch(`http://localhost/Reddit-Clone/forum_separate_components.php?postId=${this.dataset.postId}`)      //Ezt a hivatkozást nem frissíti a böngésző és ha megtenné a postID-t akkor is undefined-nak hiszi.
    
    //debuggolásként kiírod a poszt id-ját és az upvote-ok számát
    const reply = await request.json()
    console.log(`Upvoted: postID=${this.dataset.postId}, upvotes=${reply.upvotes}`)     //Mindenesetre az upvote-okat már tudja növelni eggyel.
}