class Payment {
    private double num;  // Card number
    private double cvc;  // Security code

    // Constructor
    public Payment(double num, double cvc) {
        this.num = num;
        this.cvc = cvc;
    }

    // Method to simulate payment processing
    public void on() {
        System.out.print("\nYou have successfully paid the bill.\n");
        System.out.print("Thank you!\n");
    }
}