import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;

public class Main{
	public static void main(String [] argv){
		HttpClient httpClient = HttpClientBuilder.create().build(); //Use this instead 

    try {
        HttpPost request = new HttpPost("http://localhost/sepakbola/hasil");
        StringEntity params =new StringEntity("{\"idUrut\":\"3\"}");
        request.addHeader("content-type", "application/x-www-form-urlencoded");
        request.setEntity(params);
        HttpResponse response = httpClient.execute(request);

        // handle response here...
    }catch (Exception ex) {
        // handle exception here
    } finally {
        httpClient.getConnectionManager().shutdown();
    }
	}
}