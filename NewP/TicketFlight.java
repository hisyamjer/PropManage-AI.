 class  TicketFlight
{
    public String ticket;
    public int quantity;
    public String date;
    public String place;
    public String rtime;

public TicketFlight(String ticket,int quantity,String place,String date,String rtime){
    this.ticket=ticket;
    this.quantity=quantity;
    this.place=place;
    this.date=date;
    this.rtime=rtime;
}
public void display(){
    System.out.print("\n");
    System.out.print(" Ticket reserving"+ticket+"\n");
    System.out.println("========================================================");
    System.out.println("                   TICKET RESERVATION                   ");
    System.out.println("========================================================");
    System.out.println("Place :"+place);
    System.out.println("Date :"+date);
    System.out.println("Return :"+rtime);
}
}