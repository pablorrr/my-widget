$(document).ready(function(){ 
 $("p.comment").click(function(){
        $("section#fadeincom").slideToggle( "slow" );
 });  		
 $("p.taxonomy").click(function(){
        $("section#fadein").slideToggle( "slow" );  
 });   
});