export const downloadFile=(result)=>{
    if( result !== undefined ){
        let name = 'filename.pdf';
        let type = 'application/pdf';

        if( result['headers'] !== undefined ){
            if( result['headers']['content-disposition'] !== undefined ){
                const filename = result['headers']['content-disposition'].split('=');
                if( filename[1] ){
                    name = filename[1];
                }
            }
            if( result['headers']['content-type'] !== undefined ){
                type = result['headers']['content-type'];
            }
        }

        let blob = new Blob([result.data], { type: type });
        let link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = name;
        link.target = '_blank';
        link.click();
    }
}