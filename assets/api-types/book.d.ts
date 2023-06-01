interface Book {
  id: number;
  name: string;
  imagePath?: string;
  publisher: BookPublisher;
  dataPublished: Date;
  isDeleted: boolean;
  authors?: Author[];
  tags: Tag[];
}
