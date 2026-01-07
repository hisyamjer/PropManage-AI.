public class Economy extends TicketFlight{
    boolean meals=true;
    private double weight;
    private double price;
    private double tot;
    private double mealsP;
    private double priceW;
    private double tax;
    private double total;    
public Economy(String ticket,int quantity,String place,String date,String rtime,boolean meals,double weight){
    super(ticket,quantity,place,date,rtime);
    this.meals=meals;
    this.weight=weight;
}

public void meals(){
    if(meals){
     mealsP=100.0*quantity;
    }
    else {
    mealsP=0.0;
    }
    }
    
public void lugg(){
    if(weight>20){
        priceW=30*quantity;
    }
    else if(weight>=30){
        priceW=50*quantity;
    }
    else if (weight>=50){
        priceW=100*quantity;
    }
}

public void ticket(){
    double tax=0.08;
    if(place.equalsIgnoreCase("Singapore"))
    {
        price=100.0;
        tot=price*quantity+mealsP+priceW;
        total=(tot*tax)+tot;
    }
    else if(place.equalsIgnoreCase("Jakarta"))
    {
        price=200.0;
        tot=price*quantity+mealsP+priceW;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Bangkok"))
    {
        price=300.0;
        tot=price*quantity+mealsP+priceW;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Hanoi"))
    {
        price=350.0;
        tot=price*quantity+mealsP+priceW;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Phnom Penh"))
    {
        price=365.0;
        tot=price*quantity+mealsP+priceW;
        total=(tot*tax)+tot;
    }
    else{
        System.out.print("Not found......");
    }
}

public String resit(){
    return "Total After Tax:RM "+total;
}
    
}
