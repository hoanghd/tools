//Call Func From String
function fn(){
    var result = null;
    if(arguments.length>0){
        var str = 'result = ' + arguments[0] +  '(';
        for(i=1; i<arguments.length; i++){
            if(i>1){
                str += ', ';
            }
            str += 'arguments[' + i + ']';
        }
        str += ')';
        eval(str);
    }
    
    return result;
}

function loader(){
	
}

function test(id, name){
	console.log([id, name]);
	return Math.random();
}

console.log(fn('test', 100, 'hoanghd'));