public class FirstClass extends TicketFlight{
    private double price;
    private double total;
    private double tax;
    private double tot;
    private String transport;
    
public FirstClass(String ticket,int quantity,String place,String date,String rtime,String transport){
    super(ticket,quantity,place,date,rtime);
    this.transport=transport;
}

public void meals(){
    System.out.print("Meals already provided by Airlines\n");
    }
    
public void lug(){
    System.out.print("First Class customers will get 50KG luggange weight for free\n");
}

public void ticket(){
    tax=0.06;
    if(place.equalsIgnoreCase("Singapore"))
    {
        price=10000.0;
        tot=price*quantity;
        total=(tot*tax)+tot;
    }
    else if(place.equalsIgnoreCase("Jakarta"))
    {
        price=20000.0;
        tot=price*quantity;
        total=(tot*tax)+tot;
    }    
    else if(place.equalsIgnoreCase("Bangkok"))
    {
        price=40000.0;
        tot=price*quantity;
        total=(tot*tax)+tot;
    }    
     else if(place.equalsIgnoreCase("Hanoi"))
    {
        price=35000.0;
        tot=price*quantity;
        total=(tot*tax)+tot;
    }    
     else if(place.equalsIgnoreCase("Phnom Penh"))
    {
        price=36000.0;
        tot=price*quantity;
        total=(tot*tax)+tot;
    } 
    else{
        System.out.print("Not found......");
    }
}

public void tr(){
    if(transport.equalsIgnoreCase("BMW")){
        System.out.print("Your transport is :BMW X5");
    }
    else if(transport.equalsIgnoreCase("mercedez")){
        System.out.print("Your transport is :Mercedez E-Class");
    }
    else if(transport.equalsIgnoreCase("Rolls Royce")){
        System.out.print("Your transport is :Rolls Royce Phantom");
    }
    else if(transport.equalsIgnoreCase("Lamborghini")){
        System.out.print("Your transport is :Lamborghini Uruz");
    }
    else if(transport.equalsIgnoreCase("Toyota")){
        System.out.print("Your transport is :Toyota Alphard");
    }
    else{
        System.out.print("Not in the list.....");
    }
}

public String resit(){
    return "Total :RM "+total;
}
    
}
