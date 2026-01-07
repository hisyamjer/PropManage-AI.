class User {
    public String name;
    public String icNum;
    public String phoneNum;
    public String email;

    public User(String name, String phoneNum, String icNum, String email) {
        this.name = name;
        this.phoneNum = phoneNum;
        this.icNum = icNum;
        this.email = email;
    }

    
    public String display() {
        return "\nName: " + name +"\n"+ "NRIC: " + icNum + "\n"+"Phone number: " + phoneNum +"\n"+"Email: " + email;
    }
    
    public void read(){
       System.out.print("Name : "+name+"\n");
       System.out.print("NRIC : "+icNum+"\n");
       System.out.print("Phone Number : "+phoneNum+"\n");
       System.out.print("Email : "+email);
    }
}