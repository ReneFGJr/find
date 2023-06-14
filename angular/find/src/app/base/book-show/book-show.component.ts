import { Component } from '@angular/core';

@Component({
  selector: 'app-book-show',
  templateUrl: './book-show.component.html',
  styleUrls: ['./book-show.component.scss']
})
export class BookShowComponent {
  title ="Astúncio, o estúpido esclarecido";
  authors = 'Ricardo Lacava';
  year = '2020';
  publisher = '';
  pages = '167p.';

  isbn10 = '65-88714-08-4';
  isbn13 = '978-6588-71408-9';

  description = 'asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa asxpo aposix poais xpoai sxpo iaspox iapos ixpoa isxpoa ispox iapos xipao sixpoai sxpoai spoxi aposi xpoasi xpoa isxpoai sxpoai sxpoai sxpoais xposa ';

  subjects = [{id:1, word:'Word1'},{id:2,word:'Word2'}];
}
