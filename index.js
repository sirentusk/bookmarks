addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request))
  })
  
  async function handleRequest(request) {
    // Retrieve environment variables from Wrangler configuration
    const SUPABASE_URL = ENV.SUPABASE_URL;
    const SUPABASE_KEY = ENV.SUPABASE_KEY;
  
    // Initialize Supabase client
    const supabase = createClient(SUPABASE_URL, SUPABASE_KEY);
  
    // Fetch data from Supabase tables
    try {
      const { data: bookmarks, error } = await supabase.from('MainTable').select('*');
      if (error) {
        throw error;
      }
  
      // Construct HTML table rows dynamically
      const tableRows = bookmarks.map(bookmark => {
        return `
          <tr>
            <td><img src="${bookmark.Image}" alt="Bookmark Image"></td>
            <td>${bookmark.Name}</td>
            <td>${bookmark.Tags.join(', ')}</td>
            <td>${bookmark.Authors.join(', ')}</td>
            <td>${bookmark.Channels.join(', ')}</td>
            <td><a href="${bookmark.URL}" target="_blank">${bookmark.URL}</a></td>
            <td>${bookmark.Archive}</td>
          </tr>
        `;
      });
  
      // Construct HTML table with headers and populated rows
      const tableHtml = `
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Tags</th>
              <th>Authors</th>
              <th>Channels</th>
              <th>URL</th>
              <th>Archive</th>
            </tr>
          </thead>
          <tbody>
            ${tableRows.join('')}
          </tbody>
        </table>
      `;
  
      // Respond with HTML content
      return new Response(tableHtml, {
        headers: {
          'Content-Type': 'text/html',
        },
      });
    } catch (err) {
      console.error('Error fetching data from Supabase:', err.message);
      // Respond with error message
      return new Response('An error occurred while fetching data from Supabase.', {
        status: 500,
        headers: {
          'Content-Type': 'text/plain',
        },
      });
    }
  }
  