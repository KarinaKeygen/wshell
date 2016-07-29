self.addEventListener('message', function(e) {
    w = window.open();
    w.document.write('<meta http-equiv="refresh" content="0;url='+e.data+'">');
    w.document.close();
}, false);