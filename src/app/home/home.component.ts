import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";

interface Image {
  filename: string;
  file_type: string;
}
@Component({
  selector: 'app-home2',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss'],
})
export class HomeComponent implements OnInit {
  selectedFile!: File;
  msg: string | null = null;
  images: Image[] = [];
  fileType: string = '';

  constructor(private http: HttpClient) {}

  onFileSelected(event: any): void {
    this.selectedFile = event.target.files[0];
  }

  onUpload(): void {
    if (!this.selectedFile) {
      this.msg = 'No file selected.';
      return;
    }
    const formData = new FormData();
    formData.append('uploadfile', this.selectedFile);

    this.http.post<any>('http://localhost/upload.php', formData).subscribe(
      (response) => {
        console.log(response);
        this.msg = response.msg;
        this.fetchImages();
      },
      (error) => {
        console.error(error);
        this.msg = 'Failed to upload image!';
      }
    );
  }

  ngOnInit() {
    this.fetchImages();
  }

  fetchImages(): void {
    const url = this.fileType
      ? `http://localhost/upload.php?filetype=${this.fileType}`
      : 'http://localhost/upload.php';

    this.http.get<Image[]>(url).subscribe(
      (response) => {
        console.log(response);
        this.images = response;
      },
      (error) => {
        console.error(error);
      }
    );
  }

  onFileTypeSelected(event: any): void {
    this.fileType = event.target.value;
    this.fetchImages();
  }
}
