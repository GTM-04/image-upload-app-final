import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'app-home',
  templateUrl: 'home.component.html',
  styleUrls: ['home.component.scss'],
})
export class HomeComponent {
  fileToUpload: File | null = null;
  msg: string | null = null;
  images: any[] = [];
  fileType: string | null = null;
  largeImageUrl: string | null = null;
  largeImageAlt: string | null = null;

  constructor(private http: HttpClient, private modalController: ModalController) {}

  onFileSelected(event: any) {
    this.fileToUpload = event.target.files[0] as File;
  }

  onUpload() {
    if (this.fileToUpload) {
      const formData = new FormData();
      formData.append('uploadfile', this.fileToUpload, this.fileToUpload.name);

      this.http.post<any>('http://localhost/upload.php', formData).subscribe(
        (response) => {
          this.msg = response.msg;
          this.fetchImages();
        },
        (error) => {
          console.error(error);
        }
      );
    }
  }

  fetchImages() {
    const url = 'http://localhost/fetch-images.php?filetype=' + this.fileType;
    this.http.get<any>(url).subscribe(
      (response) => {
        this.images = response.images;
      },
      (error) => {
        console.error(error);
      }
    );
  }

  showLargeImage(imageUrl: string) {
    this.largeImageUrl = imageUrl;
    this.largeImageAlt = 'Large Image';
  }

  dismissLargeImage() {
    this.largeImageUrl = null;
    this.largeImageAlt = null;
  }

  savePictureType() {
    
    console.log('Selected picture type:', this.fileType);
  }
}