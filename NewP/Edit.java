public class Edit
{
    private char ans1;
    private String nName;
    private String nIcNum;
    private String nPhoneNum;
    private String nEmail;
    
public Edit(char ans1,String nName,String nIcNum,String nPhoneNum,String nEmail){
    this.ans1=ans1;
    this.nName=nName;
    this.nIcNum=nIcNum;
    this.nPhoneNum=nPhoneNum;
    this.nEmail=nEmail;
}
public String dis(){
        return "\nName: " +nName +"\n"+ "NRIC: " + nIcNum +"\n"+"Phone number: " + nPhoneNum +"\n"+ "Email: " + nEmail;
    }
    
}
