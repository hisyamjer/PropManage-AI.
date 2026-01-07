import java.util.Scanner;
import java.io.*;
import java.io.FileNotFoundException;
import java.util.List;

public class Main {
     public static void main(String[] args) {
    Scanner scanner = new Scanner(System.in);
    File inputFile = new File("input1.txt"); 
    try{
        Scanner fileScanner = new Scanner(inputFile);
    
       
        System.out.print("Welcome to our booking Flight Managment System\n");
        System.out.print("Please fill out the form that have been given\n");
        System.out.print("Thank you\n\n");
        
        System.out.println("User Information");
        System.out.println("--------------------");
     
        //System.out.print("Your Name: ");
        //String name = scanner.nextLine();
        String name=fileScanner.nextLine().split(": ")[1];
        String phoneNum=fileScanner.nextLine().split(": ")[1];
        String icNum=fileScanner.nextLine().split(": ")[1];
        String email=fileScanner.nextLine().split(": ")[1];
        System.out.println("Name: " + name);
        System.out.println("Phone Number: " + phoneNum);
        System.out.println("IC Number: " + icNum);
        System.out.println("Email: " + email);
        System.out.println("\n");

        
         /*System.out.print("Phone Number: ");
        /String phoneNum = scanner.nextLine();

        System.out.print("MYKad/Ic: ");
        String icNum = scanner.nextLine();

        System.out.print("Email: ");
        String email = scanner.nextLine();*/

        //recall the attributes / create new object for class user//
        User[] user = new User[10];
        int userCount = 0;
        user[userCount++] = new User(name, phoneNum, icNum, email);
        
        //ask user to choose seat (subclass)//
        System.out.print("Choose seat :\n");
        System.out.print("1.Economy\n");
        System.out.print("2.Business Class\n");
        System.out.print("3.First Class\n");
        System.out.print("What seat you want? : ");
        String ticket=scanner.nextLine();
        // Declare the variable before the if-else blocks
        TicketFlight f = null;
        Admin o=new Admin();
        o.upd();
if (ticket.equalsIgnoreCase("Economy")){
Flight z=new Flight();//create new object for new flight//
z.eco();//table for economy//
System.out.print("To : ");
String place=scanner.nextLine();

System.out.print("Date day/mm/yy : "  );
String date = scanner.nextLine();

System.out.print("Return day/mm/yy : ");
String rtime = scanner.nextLine();

System.out.print("Passenger : ");
int quantity=scanner.nextInt();

System.out.print("Add on meals RM100 true/false :");
boolean meals=scanner.nextBoolean();

System.out.print("How much weight for your luggage? (KG): ");
double weight =scanner.nextDouble();

 f=new TicketFlight(ticket,quantity,place,date,rtime);
Economy e=new Economy(ticket,quantity,place,date,rtime,meals,weight);
e.meals();
e.lugg();
e.ticket();
f.display();
System.out.print("\n");
System.out.print(e.resit());
System.out.print("\n");
System.out.println(user[userCount - 1].display()); 
}
else if (ticket.equalsIgnoreCase("Business Class")){
Flight z=new Flight();
z.business();
System.out.print("To : ");
String place=scanner.nextLine();

System.out.print("Date day/mm/yy : "  );
String date = scanner.nextLine();

System.out.print("Return day/mm/yy : ");
String rtime = scanner.nextLine();

System.out.print("Passenger : ");
int quantity=scanner.nextInt();

System.out.print("Add on meals RM150 true/false :");
boolean meals=scanner.nextBoolean();

 f=new TicketFlight(ticket,quantity,place,date,rtime);
BusinessClass b=new BusinessClass(ticket,quantity,place,date,rtime,meals);
b.meals();
b.ticket();
b.lug();
f.display();
System.out.print("\n");
System.out.print(b.resit());
System.out.print("\n");
System.out.println(user[userCount - 1].display());
}
else if(ticket.equalsIgnoreCase("First Class")){
Flight z=new Flight();
z.first();
System.out.print("To : ");
String place=scanner.nextLine();

System.out.print("Date day/mm/yy : "  );
String date = scanner.nextLine();

System.out.print("Return day/mm/yy : ");
String rtime = scanner.nextLine();

System.out.print("Passenger : ");
int quantity=scanner.nextInt();

z.trans();
System.out.print("What Brand you want? :");
scanner.nextLine();
String transport=scanner.nextLine();

 f=new TicketFlight(ticket,quantity,place,date,rtime);

FirstClass d=new FirstClass(ticket,quantity,place,date,rtime,transport);

d.meals();
d.ticket();
d.lug();
d.tr();
f.display();
System.out.print("\n");
System.out.print(d.resit());
System.out.print("\n");
System.out.println(user[userCount - 1].display());
}
else{
    System.out.print("Invalid ticket.....Try again!");
}
//Payment//
 // Payment section prompts
        System.out.print("----------------------------------------------------------\n");
        System.out.print("Payment Section\n");
        System.out.print("This system only receives payment using Credit Card only.\n");

        // Prompt user to insert card number
        System.out.print("Please insert your Card Number: ");
        double num = scanner.nextDouble();

        // Prompt user to insert security code
        System.out.print("Insert your Card Security Code (e.g., 999): ");
        double cvc = scanner.nextDouble();

        // Create a Payment object and process the payment
        Payment p = new Payment(num, cvc);
        p.on();

// Continue using 'f' if necessary
if (f != null) {
    
System.out.print("Are you sure all user Information is following to your details? Y/N :");
char ans1=scanner.next().charAt(0);

if (ans1=='N'||ans1=='n')
{ 
    System.out.print("Edit your name :");
    scanner.nextLine();
    String nName=scanner.nextLine();
    
    System.out.print("Edit your IC/MyKad :");
    String nIcNum=scanner.nextLine();
    
    System.out.print("Edit your Phone Number :");
    String nPhoneNum=scanner.nextLine();
    
    System.out.print("Edit your email :");
    String nEmail=scanner.nextLine();
    
    Edit l =new Edit(ans1,nName,nIcNum,nPhoneNum,nEmail);

    System.out.print("New "+ticket+" reserving ticket\n");
    f.display();
    System.out.print("\n");
    System.out.print(l.dis());
    

}
else{
    
}
}}
catch (FileNotFoundException e) {
            System.out.println("Error: Input file not found.");
        } catch (Exception e) {
            System.out.println("Error: " + e.getMessage());
        }
    
    }
}



