# Entries

RockForms will store all form submissions under the parent `/rockforms/entries`.

You will get a list of all entries on the process page (Setup > RockForms), which will look like this:

<img src=https://i.imgur.com/uTCmI2A.png class=blur height=300>

As you can see, this list contains all form submissions from all forms.

## Entry Details

An entry that RockForms saves might look like this:

<img src=https://i.imgur.com/iLZUwL1.png class=blur>

As you can see all form data will be visible to you on this entry page. Please note that you do not have to create fields for all your form data. RockForms will save everything as json and present it in a nice way.

If you want to populate ProcessWire pages from form submissions please check out the docs about <a href=../process-input>Processing Form Submissions</a>.

## Custom List, Option 1

If you want separate lists for each form, you can create custom page listing bookmarks for each form.

For this, just go to Pages > Find. Then for `template` select `rockforms_entry` and for the field `Form` or `rockforms_entry_form` (depending on whether you choose to display field labels or field names) select the desired form name.

In our example we set `Newsletter` for the form name and get a list like this:

<img src=https://i.imgur.com/vnTUidZ.png class=blur height=300>

Once you created that list, you can save it as bookmark and then access the list from the PW backend:

<img src=https://i.imgur.com/panNobZ.png class=blur height=200>

## Custom List, Option 2

For even better lists with instant filtering and sorting you can use the [RockGrid](https://www.baumrock.com/RockGrid) module. Imagination is the only limit.
