function ldapTypeRenderer(column, type, data, dn)
{
    return "dn = " + dn + " column = " + column + " type = " + type + " data = " + data;
}
