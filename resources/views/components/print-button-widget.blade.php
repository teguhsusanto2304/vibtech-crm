<div class="btn-group" role="group" aria-label="Basic mixed styles example">
    <button type="button" class="btn btn-danger btn-sm" onclick="window.print()"><img
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAAXNSR0IArs4c6QAAAYxJREFUWEftl71KxEAUhb+Doo2tP41iucUiNlYWgrWtWFj6IOJb+AQWi9hYCpYWYmMldoIiVhZ2inBNluwyO2ZMZpPIikk7c+/9OOfOzI2YsE8TxsPfBDIzq0NJSYUCFG5IQVqgIjtchcrI7uaLjY22rAXKsy9W9totM7NlYAtYBaaTg3boFDkq6jlv3Y/9BB6AS0nPfq5vPWRm+8AxMBdZOHb7G3Ag6dQNHAEys3XgBpiKzT7m/lStrqT7QbwPdJ4A7WSLd0BvzEJFYXtAJ9t0Iil1pf/5QC/AYrbWccmLKsSsm9kacJvFPEpaCQEN36zY+yZ0skb6w3nLQifXV6gF6veNo1wjCpXpp1+17P8BNTGgVbKsBRo0YUjGJsaPspa9AzMZoDtmDEeI2Bvcszsv54ek2dDTcQ1s/HRsKwLlpb6StBkC6gIXyTy0FIKqGSgd0LaD40cKYWbzwC6w4EA1YdkTcCbpNTighVRpoqmDDsRe82X2V7E7+r+sBaqiQB2xpSyro1DZHBMH9AUIgP8l4FGXAQAAAABJRU5ErkJggg=="
            / height="20px" width="20px"></button>
    <button type="button" class="btn btn-warning btn-sm" id="exportPdf"><img
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAAXNSR0IArs4c6QAAAexJREFUWEftmL9LVWEYxz9fyKaaVJKEihbBW5Nrk1CIS2CDCYE06ChS6BSaiEvU4uaijYpCczqoQ0u4iTmIk1Okf4A1PN338t7LQc+9p/fwHrnGfeHAgfd5nvdzvs9z3l+iyZqajIfrA2RmXcAH4BlwJ1DJGUnzgT4V87oKmdkqMJwnqPd5J2kh1L8R0C+gIzTgBfspSR9DYjQCspBA3nbFp7g74RsEFRvoPfAZ+AbkgooOJGnOzO7nhYoNNCfJqUReqMKA8kIVCpQHqnCgelDlWksdOzbQLrBTZ7p4AIxW+64K6J+nrhZQllQthVoKZSmQ1R+jhpLr1D3gC3Ak6aWZtQEl4BXwFjiQ9KgKZWZufZtNQkYBAtwu8jswANwGXnugc+AEeAJMAM8llcxsCegpb0ncpOh2ALUWC2gZ2C8/L4CHQH8C6CYwCWwBG5J6zWwcuOvta4pV9s4Rlo5KyszMKTMILALbHugPcMMDrQObLmUJoCHgcREKuZQdJgKveaDfwA+nGOAGf+MV2gP60go8ikK+htKAbiWKegw49jVUKFDWnxzUH0OhoAGzjP8LoFOgPetLc/afSUo9hBZ5lG7EuippJPXvq+flLxs+lc9XT4HOnEpcdPsJfAWmJbn3S+36XMdEUiQ4TNMp9BfecPYlaKO07QAAAABJRU5ErkJggg=="
            width="20px" width="20px" /></button>
</div>
<script>
    document.getElementById("exportPdf").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');

        html2canvas(document.body, {
            scale: 2, // Improves quality
            useCORS: true
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const imgWidth = 210; // A4 width in mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width; // Maintain aspect ratio
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save("Job_Requisition_Form.pdf");
        });
    });
</script>
