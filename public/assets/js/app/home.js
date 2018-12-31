
delete home;
/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @version 0.0.1
 * @license MIT
 */
class home extends Component
{
	constructor(props) {
		super(props);
		setTitle("Tea Messenger");
		loadCss(asset("css/home.css"), 0, 1);
	}
	render() {
		var 
			rax = crt("center"),
			rdi = crt("div"), 
			rsi = crt("h2"),
			rdx = crt("div"),
			r8 = crt("img"),
			r9 = crt("div"),
			r10 = crt("table"), r11, r12, r13, r14, r15,
			rbp = {
				"First Name": "first_name",
				"Last Name": "last_name",
				"Email": "email",
				"Phone": "phone"
			};

		for(r11 in rbp) {
			r12 = crt("tr");
			r13 = crt("td");
			r13.ac(crn(r11));
			r12.ac(r13, crt("td").ac(crn(":")));
			r10.ac(r12);
		}

		r9.ac(r10);
		r8.src = "";
		r8.id = "uimg";
		rdx.ac(r8);
		rsi.id = "hll";
		rdi.ac(rsi, rdx, r9);
		rdi.id = "caged";
		rax.ac(rdi);

		return (
			rax.el
		);
	}
}

const usrTkn = localStorage.getItem("token_session");

const get_user_info = function () {
	xhr({
		before_send: function (ch) {
			ch.setRequestHeader("Authorization", "Bearer "+usrTkn);
		},
		type: "GET",
		url: config.api_url+"/home.php?action=get_user_info",
		complete: function (r) {
			try {
				r = JSON.parse(r.responseText);
				if (r["status"] === "success") {
					r = r["data"];
					domId("hll").innerHTML = "Hello "+r["first_name"]+" "+r["last_name"]+"!";
				} else {
					localStorage.removeItem("token_session");
					reroute("login");
				}
			} catch (e) {
				al("Error: "+e.message);
			}
		}
	});
};
