window.onload = function(){
  //遮蔽鍵盤事件
  document.onkeydown = function (){
    var e = window.event || arguments[0];
    //F12
    if(e.keyCode == 123){
      return false;

    //Ctrl+Shift+I
    }else if((e.ctrlKey) && (e.shiftKey) && (e.keyCode == 73)){
      return false;

    //Shift+F10
    }else if((e.shiftKey) && (e.keyCode == 121)){
      return false;

    //Ctrl+U
    }else if((e.ctrlKey) && (e.keyCode == 85)){
      return false;

    //Ctrl+C
    }else if((e.ctrlKey) && (e.keyCode == 67)){
      return false;

    //Ctrl+V
    }else if((e.ctrlKey) && (e.keyCode == 86)){
      return false;
    }

  };
  
  //遮蔽滑鼠右鍵
  document.oncontextmenu = function (){
    return false;
  }
}