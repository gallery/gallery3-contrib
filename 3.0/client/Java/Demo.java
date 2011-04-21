
import java.util.ArrayList;
import java.util.List;
import java.io.BufferedReader;
import java.io.InputStreamReader;

import com.google.gson.Gson;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.cookie.Cookie;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.protocol.HTTP;

public class Demo {
    public static final String BASE_URL = "http://example.com/gallery3/index.php";
    public static final String USERNAME = "admin";
    public static final String PASSWORD = "admin";

    public static void main(String[] argv) throws java.io.UnsupportedEncodingException, java.io.IOException {
        DefaultHttpClient httpclient = new DefaultHttpClient();
	HttpResponse response;
	Gson gson = new Gson();

	// Get the REST API key
	HttpPost post = new HttpPost(BASE_URL + "/rest");
	ArrayList<NameValuePair> nvps = new ArrayList <NameValuePair>();
	nvps.add(new BasicNameValuePair("user", USERNAME));
	nvps.add(new BasicNameValuePair("password", USERNAME));
	post.setEntity(new UrlEncodedFormEntity(nvps, HTTP.UTF_8));
	response = httpclient.execute(post);
	String api_key = gson.fromJson(new BufferedReader(
          new InputStreamReader(response.getEntity().getContent())).readLine(), String.class);
	System.out.println("API Key:" + api_key);

	// Get the JSON representation of the root album, which we know has id 1
	HttpGet get = new HttpGet(BASE_URL + "/rest/item/1");
	get.setHeader("X-Gallery-Request-Method", "GET");
	get.setHeader("X-Gallery-Request-Key", api_key);
	response = httpclient.execute(get);

	System.out.println(
          "Response: " + new BufferedReader(new InputStreamReader(response.getEntity().getContent())).readLine());
    }
}
