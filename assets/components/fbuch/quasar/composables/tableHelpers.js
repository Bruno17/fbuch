
export function useCreateTable(tableData,columns) {
    var table = document.createElement('table');
    var tableHead = document.createElement('thead');
    var tableBody = document.createElement('tbody');
    var row = null;
    var cell = null;

    row = document.createElement('tr');
    columns.forEach((field) => {
        
        cell = document.createElement('th'); 
        cell.appendChild(document.createTextNode(field));
        row.appendChild(cell);                
    });
    tableHead.appendChild(row);
    table.appendChild(tableHead);
  
    tableData.forEach((rowData) => {
      row = document.createElement('tr');
  
      columns.forEach((field) => {
        cell = document.createElement('td');
        cell.appendChild(document.createTextNode(rowData[field]));
        row.appendChild(cell);
      });
  
      tableBody.appendChild(row);
    });
  
    table.appendChild(tableBody);
    return table;
  } 

  export function useCopyTable(table){
    navigator.clipboard.write([
        new ClipboardItem({
            'text/html': new Blob([table.outerHTML], {
                type: 'text/html',
            }),
            'text/plain': new Blob([table.innerText], {
                type: 'text/plain',
            }),
        }),
    ]);    
  }