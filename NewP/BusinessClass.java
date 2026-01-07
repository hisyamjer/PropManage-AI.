public class BusinessClass extends TicketFlight{
    boolean meals=true;
    private double price;
    private double total;
    private double mealsP;
    private double tax;
    private double tot;
    
public BusinessClass(String ticket,int quantity,String place,String date,String rtime,boolean meals){
    super(ticket,quantity,place,date,rtime);
    this.meals=meals;
}

public void meals(){
    if(meals){
     mealsP=150.0*quantity;
    }
    else {
    mealsP=0.0;
    }
    }
    
public void lug(){
    System.out.print("Business Class customers will get 30KG luggange weight for free\n");
}

public void ticket(){
    tax=0.06;
    if(place.equalsIgnoreCase("Singapore"))
    {
        price=390.0;
        tot=price*quantity+mealsP;
        total=(tot*tax)+tot;
    }
    else if(place.equalsIgnoreCase("Jakarta"))
    {
        price=410.0;
        tot=price*quantity+mealsP;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Bangkok"))
    {
        price=500.0;
        tot=price*quantity+mealsP;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Hanoi"))
    {
        price=650.0;
        tot=price*quantity+mealsP;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Phnom Penh"))
    {
        price=710.0;
        tot=price*quantity+mealsP;
        total=(tot*tax)+tot;
    }
    else{
        System.out.print("Not found......");
    }
}

public String resit(){
    return "Total After Tax :RM "+total;
}
    
}
