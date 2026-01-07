class Flight  {
    
public void eco()
    {

 String[] availablePlace = new String[10];
 int flightCount=0;
 
availablePlace[flightCount++] = new String("Bangkok           |RM 300");
availablePlace[flightCount++] = new String("Jakarta           |RM 200");
availablePlace[flightCount++] = new String("Singapore         |RM 100");
availablePlace[flightCount++] = new String("Hanoi             |RM 350");
availablePlace[flightCount++] = new String("Phnom Penh        |RM 365");

System.out.print("Available Place  |Price\n");
System.out.print("---------------   --------\n");
        for (int i = 0; i < flightCount; i++) {
            System.out.println(availablePlace[i]);
            System.out.print("\n");
            }
        }
public void business()
    {

 String[] availablePlace = new String[10];
 int flightCount=0;
 
availablePlace[flightCount++] = new String("Bangkok           |RM 500");
availablePlace[flightCount++] = new String("Jakarta           |RM 410");
availablePlace[flightCount++] = new String("Singapore         |RM 390");
availablePlace[flightCount++] = new String("Hanoi             |RM 650");
availablePlace[flightCount++] = new String("Phnom Penh        |RM 710");

System.out.print("Available Place  |Price\n");
System.out.print("---------------   --------\n");
        for (int i = 0; i < flightCount; i++) {
            System.out.println(availablePlace[i]);
            System.out.print("\n");
            }
        }
public void first()
    {

 String[] availablePlace = new String[10];
 int flightCount=0;
 
availablePlace[flightCount++] = new String("Bangkok           |RM 40,000");
availablePlace[flightCount++] = new String("Jakarta           |RM 20,000");
availablePlace[flightCount++] = new String("Singapore         |RM 10,000");
availablePlace[flightCount++] = new String("Hanoi             |RM 35,000");
availablePlace[flightCount++] = new String("Phnom Penh        |RM 36,000");

System.out.print("Available Place  |Price\n");
System.out.print("---------------   --------\n");
        for (int i = 0; i < flightCount; i++) {
            System.out.println(availablePlace[i]);
            System.out.print("\n");
            }
        }
public void trans(){
 String[] table = new String[10];
 int flightCount=0;
 
table[flightCount++] = new String("BMW           |X5");
table[flightCount++] = new String("Mercedes      |E-Class");
table[flightCount++] = new String("Rolls Royce   |Phantom");
table[flightCount++] = new String("Lamborghini   |Uruz");
table[flightCount++] = new String("Toyota        |Alphard");

System.out.print("Please choose the brand car \n");
System.out.print("Brand         |Model\n");
System.out.print("------------   --------\n");
        for (int i = 0; i < flightCount; i++) {
            System.out.println(table[i]);
            System.out.print("\n");
            }
            
        }

}
