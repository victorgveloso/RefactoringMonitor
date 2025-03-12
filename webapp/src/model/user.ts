export class User {

    constructor(private userID, private userName, private name: string, private familyName: string, private role: string, private email: string, private jwt: string) {
      if (!this.name.length && this.userName.length > 0) {
        this.name = this.userName;
      }
      if (!this.familyName.length) {
        this.familyName = "";
      }
      if (!this.role.length) {
        this.role = "USER";
      }
    }

    public getUserID() { return this.userID; }
    public getUserName() { return this.userName; }
    public getFullName() { return this.name + " " + this.familyName; }
    public getRole() { return this.role; }
    public getEmail() { return this.email; }
    public getJWT() { return this.jwt; }

    public isAdmin() { return this.role.toUpperCase() == "ADMIN"; }

}
