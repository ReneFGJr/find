import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { FooterComponent } from './010_header/footer/footer.component';
import { HeaderComponent } from './010_header/header/header.component';
import { NavbarComponent } from './010_header/navbar/navbar.component';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';
import { BookViewComponent } from './020_find/widget/book-view/book-view.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { HttpClientModule } from '@angular/common/http';
import { BookComponent } from './020_find/widget/book/book.component';
import { CatalogComponent } from './020_find/widget/catalog/catalog.component';
import { LibrariesComponent } from './020_find/widget/libraries/libraries.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { WaitingComponent } from './020_find/widget/waiting/waiting.component';
import { WebcamComponent } from './020_find/io/webcam/webcam.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TomboComponent } from './040_tombo/page/tombo/tombo.component';
import { TomboViewComponent } from './040_tombo/widgat/tombo-view/tombo-view.component';
import { TomboNotFoundComponent } from './040_tombo/widgat/tombo-not-found/tombo-not-found.component';
import { BookLabelComponent } from './040_tombo/widgat/book-label/book-label.component';
import { TomboShowComponent } from './040_tombo/widgat/tombo-show/tombo-show.component';
import { BookLabelColorComponent } from './040_tombo/widgat/book-label-color/book-label-color.component';
import { BookLabelLibraryComponent } from './040_tombo/widgat/book-label-library/book-label-library.component';
import { BookStatusComponent } from './040_tombo/widgat/book-status/book-status.component';
import { BookShowArrayComponent } from './040_tombo/widgat/book-show-array/book-show-array.component';
import { SearchFormComponent } from './040_tombo/widgat/search-form/search-form.component';
import { ReportItemComponent } from './020_find/report/report-item/report-item.component';
import { ReportsComponent } from './020_find/page/reports/reports.component';
import { TomboLabelComponent } from './030_admin/tombo/tombo-label/tombo-label.component';
import { TomboEditComponent } from './030_admin/tombo/tombo-edit/tombo-edit.component';
import { LabelTomboComponent } from './040_tombo/page/label-tombo/label-tombo.component';
import { UsersComponent } from './050_users/page/users/users.component';
import { GroupsComponent } from './050_users/page/groups/groups.component';
import { UserListComponent } from './050_users/widget/user-list/user-list.component';
import { UserShowComponent } from './050_users/widget/user-show/user-show.component';
import { UserEditComponent } from './050_users/widget/user-edit/user-edit.component';
import { UserIconComponent } from './050_users/widget/user-icon/user-icon.component';
import { UserAuthComponent } from './050_users/widget/user-auth/user-auth.component';
import { UserLogoutComponent } from './050_users/widget/user-logout/user-logout.component';
import { UserAwardsComponent } from './050_users/widget/user-awards/user-awards.component';
import { UserFunctionsComponent } from './050_users/widget/user-functions/user-functions.component';
import { UserHistoryComponent } from './050_users/widget/user-history/user-history.component';
import { UserFavoritesComponent } from './050_users/widget/user-favorites/user-favorites.component';
import { UserGroupsComponent } from './050_users/widget/user-groups/user-groups.component';
import { UserGroupsAddComponent } from './050_users/widget/user-groups-add/user-groups-add.component';
import { UserGroupsHeaderComponent } from './050_users/widget/user-groups-header/user-groups-header.component';
import { UserGroupsAssignUsersComponent } from './050_users/widget/user-groups-assign-users/user-groups-assign-users.component';
import { WorkFormComponent } from './020_find/widget/work-form/work-form.component';
import { ExpressionShowComponent } from './020_find/widget/expression-show/expression-show.component';
import { ExpressionLanguageComponent } from './020_find/widget/expression-language/expression-language.component';
import { CatalogInputFormComponent } from './060_catalog/widget/catalog-input-form/catalog-input-form.component';
import { CatalogItemShowComponent } from './060_catalog/widget/catalog-item-show/catalog-item-show.component';

@NgModule({
  declarations: [
    AppComponent,
    VitrineComponent,
    FooterComponent,
    HeaderComponent,
    NavbarComponent,
    SpashPageComponent,
    BookViewComponent,
    SelectLibraryComponent,
    BookComponent,
    CatalogComponent,
    LibrariesComponent,
    WaitingComponent,
    WebcamComponent,
    TomboComponent,
    TomboViewComponent,
    TomboNotFoundComponent,
    BookLabelComponent,
    TomboShowComponent,
    BookLabelColorComponent,
    BookLabelLibraryComponent,
    BookStatusComponent,
    BookShowArrayComponent,
    SearchFormComponent,
    ReportsComponent,
    ReportItemComponent,
    TomboLabelComponent,
    TomboEditComponent,
    LabelTomboComponent,
    UsersComponent,
    GroupsComponent,
    UserListComponent,
    UserShowComponent,
    UserEditComponent,
    UserIconComponent,
    UserAuthComponent,
    UserLogoutComponent,
    UserAwardsComponent,
    UserFunctionsComponent,
    UserHistoryComponent,
    UserFavoritesComponent,
    UserGroupsComponent,
    UserGroupsAddComponent,
    UserGroupsHeaderComponent,
    UserGroupsAssignUsersComponent,
    WorkFormComponent,
    ExpressionShowComponent,
    ExpressionLanguageComponent,
    CatalogInputFormComponent,
    CatalogItemShowComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    NgbModule,
    BrowserAnimationsModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
