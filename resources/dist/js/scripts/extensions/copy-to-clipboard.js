var userText=$("#copy-to-clipboard-input"),btnCopy=$("#btn-copy");btnCopy.on("click",function(){userText.select(),document.execCommand("copy")});
